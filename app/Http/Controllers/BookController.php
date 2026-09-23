<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Rating;
use App\Services\OpenLibraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    // Browse/search books. Supports three modes, combinable:
    // - no query, no category: every book (browse all)
    // - category: filter by the real book_categories relationship
    // - query: keyword match on title/author
    //
    // When a keyword search returns few local hits we also fetch Open
    // Library suggestions. These are DISPLAY ONLY — nothing is written to
    // the database. An admin adds them deliberately via the admin form.
    public function search(Request $request, OpenLibraryService $openLibrary)
    {
        $query = trim((string) $request->input('query', ''));
        $categoryId = $request->input('category');
        $publicDomainOnly = $request->boolean('domain');

        $booksQuery = Book::where('is_archived', false)
        ->with(['authors', 'categories']);

        if ($categoryId) {
            $booksQuery->whereHas('categories', function ($c) use ($categoryId) {
                $c->where('book_categories.cate_id', $categoryId);
            });
        }

        if ($publicDomainOnly) {
            $booksQuery->where('copyright_status', 'public_domain');
        }

        if ($query !== '') {
            $booksQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhereHas('authors', function ($a) use ($query) {
                        $a->where('name', 'LIKE', "%{$query}%");
                    });
            });
        }

        $books = $booksQuery->latest('book_id')->get();

        // Suggestions from Open Library: shown as "not in our library yet",
        // never inserted. Only for real keyword searches (not category
        // browsing) and only when the local shelf is thin.
        $suggestions = collect();

        if ($query !== '' && ! $categoryId && ! $publicDomainOnly && $books->count() < 10) {
            $existingKeys = Book::whereNotNull('open_library_key')
                ->pluck('open_library_key')
                ->all();

            $localTitles = $books->map(fn ($b) => strtolower($b->title))->all();

            $suggestions = collect($openLibrary->search($query, 12))
                ->filter(fn ($doc) => ! empty($doc['key']) && ! empty($doc['title']))
                ->reject(fn ($doc) => in_array($doc['key'], $existingKeys, true))
                ->reject(fn ($doc) => in_array(strtolower($doc['title']), $localTitles, true))
                ->map(fn ($doc) => [
                    'key' => $doc['key'],
                    'title' => $doc['title'],
                    'authors' => implode(', ', array_slice($doc['author_name'] ?? [], 0, 2)) ?: 'Unknown author',
                    'year' => $doc['first_publish_year'] ?? null,
                    'cover' => $openLibrary->coverUrl($doc['cover_i'] ?? null, 'M'),
                ])
                ->take(6)
                ->values();
        }

        $categories = BookCategory::orderBy('cate_name')->get();

        return view('books.index', compact('books', 'query', 'categoryId', 'categories', 'suggestions', 'publicDomainOnly'));
    }

    public function uploadEpub(Request $request, Book $book)
    {
        $request->validate(['epub_file' => 'required|file|max:51200']);

        $file = $request->file('epub_file');
        if (strtolower($file->getClientOriginalExtension()) !== 'epub') {
            return response()->json(['message' => 'File must be a .epub file'], 422);
        }

        if ($book->file_path && Storage::disk('local')->exists($book->file_path)) {
            Storage::disk('local')->delete($book->file_path);
        }

        $path = $file->store('epubs', 'local');
        $book->update(['file_path' => $path]);

        return response()->json(['message' => 'EPUB uploaded successfully', 'path' => $path], 200);
    }

    public function storeReview(Request $request, Book $book)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);

        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $book->book_id],
            ['score' => $validated['rating'], 'review' => $validated['review'] ?? null]
        );

        return response()->json(['message' => 'Review saved successfully', 'data' => $rating], 200);
    }

    public function getReviews(Book $book)
    {
        $book->load(['ratings.user:user_id,username']);

        return response()->json([
            'average_rating' => $book->averageRating(),
            'total_reviews' => $book->ratings->count(),
            'reviews' => $book->ratings,
        ], 200);
    }
}
