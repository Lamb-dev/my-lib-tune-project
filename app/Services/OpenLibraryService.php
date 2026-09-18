<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenLibraryService
{
    public function search(string $query, int $limit = 10): array
    {
        try {
            $response = Http::timeout(12)->retry(2, 300)->get('https://openlibrary.org/search.json', [
                'q' => $query,
                'limit' => $limit,
                'fields' => 'key,title,author_name,first_publish_year,cover_i,subject,ia,ebook_access',
            ]);

            return $response->successful() ? $response->json('docs', []) : [];
        } catch (\Throwable $e) {
            Log::warning('Open Library search failed: ' . $e->getMessage());
            return [];
        }
    }

    public function fetchWorkDetails(string $workKey): array
    {
        try {
            $response = Http::timeout(12)->retry(2, 300)->get("https://openlibrary.org{$workKey}.json");

            if (! $response->successful()) {
                return ['description' => null, 'subjects' => []];
            }

            $description = $response->json('description');
            if (is_array($description)) {
                $description = $description['value'] ?? null;
            }

            return [
                'description' => $description,
                'subjects' => $response->json('subjects', []),
            ];
        } catch (\Throwable $e) {
            Log::warning('Open Library work fetch failed: ' . $e->getMessage());
            return ['description' => null, 'subjects' => []];
        }
    }

    public function categoryFromSubjects(array $subjects): int
    {
        $map = [
            'Science Fiction' => ['science fiction'],
            'Fantasy' => ['fantasy'],
            'Mystery' => ['mystery', 'detective'],
            'Romance' => ['romance'],
            'Gothic & Horror' => ['horror', 'gothic'],
            'Poetry' => ['poetry', 'poems'],
            'Biography' => ['biography', 'autobiography'],
            'History' => ['history'],
            'Philosophy' => ['philosophy'],
            'Adventure' => ['adventure'],
            'Classic Literature' => ['classic literature', 'fiction'],
        ];

        $subjectsLower = array_map('strtolower', $subjects);

        foreach ($map as $categoryName => $keywords) {
            foreach ($keywords as $keyword) {
                foreach ($subjectsLower as $subject) {
                    if (str_contains($subject, $keyword)) {
                        return BookCategory::firstOrCreate(['cate_name' => $categoryName])->cate_id;
                    }
                }
            }
        }

        return BookCategory::firstOrCreate(['cate_name' => 'General'])->cate_id;
    }

    /**
     * Open Library returns author names with whatever casing/spacing the
     * original catalog entry used, which can easily drift from a name an
     * admin already entered by hand ("J.K. Rowling" vs "J. K. Rowling").
     * A plain firstOrCreate() does an exact match and would happily create
     * a near-duplicate author row for a difference no reader would ever
     * notice — so this checks case- and whitespace-insensitively first.
     */
    private function findOrCreateAuthor(string $name): Author
    {
        $normalized = trim($name);

        return Author::whereRaw('LOWER(TRIM(name)) = ?', [strtolower($normalized)])->first()
            ?? Author::create(['name' => $normalized]);
    }

    /**
     * Turn Open Library search results into local Book rows. Authors are
     * attached via the book_authors pivot (not a single auth_id column),
     * matching the current schema. Imported books default to
     * copyright_status = 'copyrighted' — catalog-only until someone
     * manually marks a book public_domain and attaches a real file/link.
     */
    public function importResults(array $docs, ?int $fallbackCategoryId = null): Collection
    {
        return collect($docs)->map(function ($doc) use ($fallbackCategoryId) {
            if (empty($doc['key']) || empty($doc['title'])) {
                return null;
            }

            $authorName = $doc['author_name'][0] ?? 'Unknown author';
            $authorId = $this->findOrCreateAuthor($authorName)->auth_id;

            $categoryId = ! empty($doc['subject'])
                ? $this->categoryFromSubjects($doc['subject'])
                : $fallbackCategoryId;

            $isPublicDomain = $this->isPublicDomain($doc);
            $copyrightStatus = $isPublicDomain ? 'public_domain' : 'copyrighted';
            $readingUrl = null;

            if ($isPublicDomain) {
                $readingUrl = $this->getReadableUrl($doc['key']);
            }

            $book = Book::firstOrCreate(
                ['open_library_key' => $doc['key']],
                [
                    'title' => $doc['title'],
                    'published_year' => $doc['first_publish_year'] ?? null,
                    'cate_id' => $categoryId,
                    'copyright_status' => $copyrightStatus,
                    'reading_url' => $readingUrl,
                    'cover_image' => $this->coverUrl($doc['cover_i'] ?? null),
                ]
            );

            if (! $book->cate_id && $categoryId) {
                $book->update(['cate_id' => $categoryId]);
            }

            if ($categoryId && ! $book->categories()->where('book_categories.cate_id', $categoryId)->exists()) {
                $book->categories()->attach($categoryId);
            }

            if (! $book->authors()->where('authors.auth_id', $authorId)->exists()) {
                $book->authors()->attach($authorId);
            }

            return $book;
        })->filter();
    }

    /**
     * Get a readable URL (EPUB or PDF) for a work by checking its editions and
     * querying the Internet Archive for a full-text copy.
     *
     * @param string $workKey
     * @return string|null
     */
    public function getReadableUrl(string $workKey): ?string
    {
        try {
            $response = Http::timeout(5)->get("https://openlibrary.org{$workKey}/editions.json", ['limit' => 20]);
            if (! $response->successful()) {
                return null;
            }
            $editions = $response->json('entries', []);
            foreach ($editions as $edition) {
                $identifiers = $edition['identifiers'] ?? [];
                $isbn = null;
                if (!empty($identifiers['isbn_10'])) {
                    $isbn = $identifiers['isbn_10'][0];
                } elseif (!empty($identifiers['isbn_13'])) {
                    $isbn = $identifiers['isbn_13'][0];
                }
                if ($isbn) {
                    $iaResponse = Http::timeout(8)->get(
                        "https://archive.org/advancedsearch.php",
                        [
                            'q' => 'isbn:' . $isbn . ' AND mediatype:texts',
                            'fl' => 'identifier,format',
                            'rows' => '5',
                            'output' => 'json'
                        ]
                    );
                    if ($iaResponse->successful()) {
                        $docs = $iaResponse->json('response.docs', []);
                        foreach ($docs as $doc) {
                            $formats = $doc['format'] ?? [];
                            if (is_array($formats) && in_array('EPUB', $formats, true)) {
                                $identifier = $doc['identifier'];
                                return "https://archive.org/download/{$identifier}/{$identifier}_epub.epub";
                            }
                            elseif (is_array($formats) && in_array('PDF', $formats, true)) {
                                $identifier = $doc['identifier'];
                                return "https://archive.org/download/{$identifier}/{$identifier}.pdf";
                            }
                        }
                    }
                }
            }
            return null;
        } catch (\Throwable $e) {
            Log::warning('Failed to get readable URL for work ' . $workKey . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Open Library's own signal for "can be read in full, for free, right
     * now" is ebook_access === 'public' (as opposed to 'borrowable',
     * 'printdisabled', or 'no_ebook'). That's a much more reliable test
     * than guessing from publish year — plenty of pre-1928 works are
     * still marked non-public because no scan exists, and some newer
     * works are genuinely public domain (government publications, CC0).
     */
    public function isPublicDomain(array $doc): bool
    {
        return ($doc['ebook_access'] ?? null) === 'public';
    }

    /**
     * A direct, working link to read the book, built from its Internet
     * Archive identifier. Only meaningful when isPublicDomain() is true —
     * a borrowable-only book would just send readers to a login wall.
     */
    public function readingUrlFromDoc(array $doc): ?string
    {
        $identifier = $doc['ia'][0] ?? null;

        return $identifier ? "https://archive.org/details/{$identifier}" : null;
    }

    public function coverUrl(?int $coverId, string $size = 'L'): ?string
    {
        return $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-{$size}.jpg" : null;
    }
}