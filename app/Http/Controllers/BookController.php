<?php

namespace App\Http\Controllers;

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
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $book->ratings()->updateOrCreate(
            [
                'user_id' => auth()->id(),
            ],
            [
                'rating' => $request->input('rating'),
                'review' => $request->input('review'),
            ]
        );

        return back()->with('success', 'Review submitted successfully.');
    }
}
