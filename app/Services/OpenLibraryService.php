<?php

namespace App\Services;

<<<<<<< Updated upstream
use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    protected string $baseUrl = 'https://openlibrary.org';

    public function search(string $query, int $limit = 20): array
    {
        $query = trim($query);

        $response = Http::withoutRedirecting()
            ->withHeaders([
                'User-Agent' => 'LibTune/1.0',
                'Accept' => 'application/json',
            ])
            ->timeout(10)
            ->get(
                $this->baseUrl . '/search.json?q=' . urlencode($query) . '&limit=' . $limit
            );

        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        return $data['docs'] ?? [];
    }
}
=======
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenLibraryService
{
    /**
     * Query Open Library's search API. Now also requests 'subject' —
     * real subject tags per book (e.g. "Fantasy fiction", "Biography")
     * that we use to assign a real category, not just a guess based on
     * which search term happened to surface the book.
     */
    public function search(string $query, int $limit = 10): array
    {
        try {
            $response = Http::timeout(5)->get('https://openlibrary.org/search.json', [
                'q' => $query,
                'limit' => $limit,
                'fields' => 'key,title,author_name,first_publish_year,cover_i,subject',
            ]);

            return $response->successful() ? $response->json('docs', []) : [];
        } catch (\Throwable $e) {
            Log::warning('Open Library search failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch a book's description AND subject tags from its Open Library
     * "work" page in a single request — used both for the lazy
     * description fetch on the detail page, and for the backfill sweep.
     */
    public function fetchWorkDetails(string $workKey): array
    {
        try {
            $response = Http::timeout(5)->get("https://openlibrary.org{$workKey}.json");

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

    /**
     * Map real subject tags to one of our categories, creating the
     * category if it doesn't exist yet. Always returns a real category
     * id — books with no recognizable subject land in a "General"
     * catch-all rather than staying uncategorized forever.
     */
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
     * Turn Open Library search results into local Book rows (creating
     * authors as needed). Category is derived from the book's own
     * subject tags when available, falling back to $fallbackCategoryId
     * (used by the bulk-import command's fixed topic-to-category map).
     *
     * Imported books default to is_copyrighted = true with no file_path:
     * catalog-only until someone manually attaches a real public-domain
     * epub and flips that flag.
     */
    public function importResults(array $docs, ?int $fallbackCategoryId = null): Collection
    {
        return collect($docs)->map(function ($doc) use ($fallbackCategoryId) {
            if (empty($doc['key']) || empty($doc['title'])) {
                return null;
            }

            $authorName = $doc['author_name'][0] ?? 'Unknown author';
            $authorId = Author::firstOrCreate(['name' => $authorName])->auth_id;

            $categoryId = ! empty($doc['subject'])
                ? $this->categoryFromSubjects($doc['subject'])
                : $fallbackCategoryId;

            $book = Book::firstOrCreate(
                ['open_library_key' => $doc['key']],
                [
                    'title' => $doc['title'],
                    'published_year' => $doc['first_publish_year'] ?? null,
                    'auth_id' => $authorId,
                    'cate_id' => $categoryId,
                    'is_copyrighted' => true,
                    'file_path' => null,
                    'cover_image' => $this->coverUrl($doc['cover_i'] ?? null),
                ]
            );

            if (! $book->cate_id && $categoryId) {
                $book->update(['cate_id' => $categoryId]);
            }

            return $book;
        })->filter();
    }

    public function coverUrl(?int $coverId, string $size = 'L'): ?string
    {
        return $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-{$size}.jpg" : null;
    }
}
>>>>>>> Stashed changes
