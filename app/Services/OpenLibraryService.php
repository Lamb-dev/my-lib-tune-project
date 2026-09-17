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
            $response = Http::timeout(15)->retry(2, 300)->get('https://openlibrary.org/search.json', [
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

    public function fetchWorkDetails(string $workKey): array
    {
        try {
            $response = Http::timeout(15)->retry(2, 300)->get("https://openlibrary.org{$workKey}.json");

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
            $authorId = Author::firstOrCreate(['name' => $authorName])->auth_id;

            $categoryId = ! empty($doc['subject'])
                ? $this->categoryFromSubjects($doc['subject'])
                : $fallbackCategoryId;

            $book = Book::firstOrCreate(
                ['open_library_key' => $doc['key']],
                [
                    'title' => $doc['title'],
                    'published_year' => $doc['first_publish_year'] ?? null,
                    'cate_id' => $categoryId,
                    'copyright_status' => 'copyrighted',
                    'reading_url' => null,
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

    public function coverUrl(?int $coverId, string $size = 'L'): ?string
    {
        return $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-{$size}.jpg" : null;
    }
}
