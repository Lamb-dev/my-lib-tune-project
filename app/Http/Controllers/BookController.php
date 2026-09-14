<?php
 
namespace App\Http\Controllers;
<<<<<<< Updated upstream

use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OpenLibraryService;
class BookController extends Controller
{
    public function search(Request $request, OpenLibraryService $openLibrary)
{
      $query = trim($request->input('q', ''));

    $results = [];

    if ($query !== '') {
        $results = $openLibrary->search($query, 24);
    }

    return view('books.search', compact('results', 'query'));
}

    public function getReviews(Book $book)
    {
        $reviews = $book->ratings()
            ->with('user')
            ->latest()
            ->paginate(20);

        return response()->json($reviews);
    }

    public function storeReview(Request $request, Book $book)
=======
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Rating;
use App\Services\OpenLibraryService;
 
class BookController extends Controller
{
    // Browse/search books. Supports three modes, combinable:
    // - no query, no category: every book (browse all)
    // - category: filter by the real book_categories relationship
    // - query: keyword match on title/author, supplemented by Open
    //   Library when local results are thin (only for real searches,
    //   not category browsing — no reason to hit an external API
    //   just because someone clicked "Romance").
    public function search(Request $request, OpenLibraryService $openLibrary)
    {
        $query = trim((string) $request->input('query', ''));
        $categoryId = $request->input('category');
 
        $booksQuery = Book::with(['author', 'category']);
 
        if ($categoryId) {
            $booksQuery->where('cate_id', $categoryId);
        }
 
        if ($query !== '') {
            $booksQuery->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhereHas('author', function ($a) use ($query) {
                        $a->where('name', 'LIKE', "%{$query}%");
                    });
            });
        }
 
        $books = $booksQuery->latest('book_id')->get();
 
        if ($query !== '' && $books->count() < 10) {
            $imported = $openLibrary->importResults($openLibrary->search($query));
            $books = $books->merge($imported)->unique('book_id')->values();
        }
 
        $categories = BookCategory::orderBy('cate_name')->get();
 
        return view('books.index', compact('books', 'query', 'categoryId', 'categories'));
    }
 
    // Handle EPUB Upload & Server Storage
    public function uploadEpub(Request $request, $id)
>>>>>>> Stashed changes
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);
<<<<<<< Updated upstream

        $book->ratings()->updateOrCreate(
=======
 
        $file = $request->file('epub_file');
        if (strtolower($file->getClientOriginalExtension()) !== 'epub') {
            return response()->json(['message' => 'File must be a .epub file'], 422);
        }
 
        $book = Book::findOrFail($id);
 
        // Delete the previous EPUB file if re-uploading.
        if ($book->file_path && Storage::disk('local')->exists($book->file_path)) {
            Storage::disk('local')->delete($book->file_path);
        }
 
        $path = $file->store('epubs', 'local');
 
        $book->update(['file_path' => $path]);
 
        return response()->json([
            'message' => 'EPUB uploaded successfully',
            'path' => $path,
        ], 200);
    }
 
    // Save or update the current user's rating & review for a book.
    public function storeReview(Request $request, $id)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);
 
        $rating = Rating::updateOrCreate(
            ['user_id' => Auth::id(), 'book_id' => $id],
>>>>>>> Stashed changes
            [
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $request->input('rating'),
                'review' => $request->input('review'),
            ]
        );
<<<<<<< Updated upstream

        return back()->with('success', 'Review submitted successfully.');
=======
 
        return response()->json([
            'message' => 'Review saved successfully',
            'data' => $rating,
        ], 200);
    }
 
    // Fetch a book's ratings/reviews plus the aggregated average.
    public function getReviews($id)
    {
        $book = Book::with(['ratings.user:user_id,username'])->findOrFail($id);
 
        return response()->json([
            'average_rating' => $book->averageRating(),
            'total_reviews' => $book->ratings->count(),
            'reviews' => $book->ratings,
        ], 200);
>>>>>>> Stashed changes
    }
}