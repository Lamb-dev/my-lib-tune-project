<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ProgressBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookReaderController extends Controller
{
    /**
     * Show the in-browser reader page for a book.
     * GET /books/{book}/read
     */
    public function read(Book $book)
    {
        abort_unless($book->isReadable(), 403, 'This book is not available to read online.');

        $progress = ProgressBook::where('user_id', auth()->id())
            ->where('book_id', $book->book_id)
            ->first();

        return view('books.read', [
            'book' => $book,
            'fileUrl' => route('books.file', $book),
            'progressUrl' => route('books.progress', $book),
            'lastCfi' => $progress->last_read ?? null,
        ]);
    }

    /**
     * Stream the actual .epub file bytes to epub.js.
     * GET /books/{book}/file
     *
     * This is deliberately its own authorized route rather than a public
     * storage link — it re-checks isReadable() so a copyrighted or
     * unpublished book can't be fetched just by knowing/guessing the URL.
     */
    public function stream(Book $book)
    {
        abort_unless($book->isReadable(), 403, 'This book is not available to read online.');
        abort_unless(Storage::disk('local')->exists($book->file_path), 404, 'Book file not found.');

        return Storage::disk('local')->response($book->file_path, null, [
            'Content-Type' => 'application/epub+zip',
        ]);
    }

    /**
     * Save (or update) how far the current user has read.
     * POST /books/{book}/progress
     */
    public function saveProgress(Request $request, Book $book)
    {
        abort_unless($book->isReadable(), 403);

        $validated = $request->validate([
            'cfi' => 'required|string',
        ]);

        ProgressBook::updateOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $book->book_id],
            ['last_read' => $validated['cfi']]
        );

        return response()->json(['saved' => true]);
    }
}
