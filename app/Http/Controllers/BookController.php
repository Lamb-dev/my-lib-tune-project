<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;
use App\Models\Rating;

class BookController extends Controller
{
    // Search books by title, or by their author's name via the authors relation.
    public function search(Request $request)
    {
        $query = $request->input('query');

        $books = Book::where('title', 'LIKE', "%{$query}%")
            ->orWhereHas('author', function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%");
            })
            ->get();

        return view('books.index', compact('books', 'query'));
    }

    // Handle EPUB Upload & Server Storage
    public function uploadEpub(Request $request, $id)
    {
        $request->validate([
            // 'mimes:epub' can be unreliable since epub isn't in Symfony's
            // default MIME map — validating the extension directly is safer.
            'epub_file' => 'required|file|max:51200', // 50MB limit
        ]);

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
            [
                'score' => $validated['rating'],
                'review' => $validated['review'] ?? null,
            ]
        );

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
    }
}
