<?php
namespace App\Http\Controllers;

use App\Models\ProgressBook;
use Illuminate\View\View;

class SavedBookController extends Controller
{
    public function index(): View
    {
        $userId = auth()->id();

        $books = auth()->user()
            ->savedBooks()
            ->where('is_archived', false)
            ->with(['authors', 'categories'])
            ->withCount('ratings')
            ->latest('saved_books.created_at')
            ->get();

        // One progress row per book_id for this user, keyed by book_id, so
        // each saved book below can be sorted into a shelf without a query
        // per book.
        $progressByBook = ProgressBook::where('user_id', $userId)
            ->whereIn('book_id', $books->pluck('book_id'))
            ->get()
            ->keyBy('book_id');

        // Started but not finished — this is the "pick up where you left
        // off" shelf, most recently read first. A progress row exists here
        // either because the epub reader saved real progress, or because
        // the reader explicitly marked the book as "Currently reading" on
        // its detail page.
        $currentlyReading = $books
            ->filter(function ($book) use ($progressByBook) {
                $progress = $progressByBook->get($book->book_id);
                return $progress && ! $progress->is_finished;
            })
            ->sortByDesc(fn ($book) => $progressByBook->get($book->book_id)->updated_at)
            ->values();

        // Finished books.
        $finished = $books
            ->filter(fn ($book) => optional($progressByBook->get($book->book_id))->is_finished)
            ->values();

        // Saved but never opened yet.
        $nextUp = $books
            ->reject(fn ($book) => $progressByBook->has($book->book_id))
            ->values();

        $savedBooks = $books;

        return view('library.index', compact('books', 'currentlyReading', 'nextUp', 'finished', 'savedBooks'));
    }
}
