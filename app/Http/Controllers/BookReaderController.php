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
        abort_if($book->is_archived, 404);
        abort_unless($book->hasEpubFile(), 403, 'This book has no reader file available — try "View source link" on its page instead.');

        $progress = ProgressBook::where('user_id', auth()->id())
            ->where('book_id', $book->book_id)
            ->first();

        return view('books.read', [
            'book' => $book,
            'fileUrl' => route('books.file', $book),
            'progressUrl' => route('books.progress', $book),
            'finishedUrl' => route('books.finished', $book),
            'lastCfi' => $progress->last_read ?? null,
            'isFinished' => (bool) ($progress->is_finished ?? false),
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
        abort_if($book->is_archived, 404);
        abort_unless($book->hasEpubFile(), 404, 'Book file not found.');

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
        abort_unless($book->hasEpubFile(), 403);

        $validated = $request->validate([
            'cfi' => 'required|string',
        ]);

        ProgressBook::updateOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $book->book_id],
            ['last_read' => $validated['cfi']]
        );

        return response()->json(['saved' => true]);
    }

    /**
     * Mark a book finished/unfinished for the current reader. Creates a
     * progress row if one doesn't exist yet (e.g. someone reading via
     * "View source link" who never triggered an epub progress save).
     * POST /books/{book}/finished
     */
    public function toggleFinished(Book $book)
    {
        $progress = ProgressBook::firstOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $book->book_id]
        );

        $progress->update(['is_finished' => ! $progress->is_finished]);

        return response()->json(['finished' => $progress->is_finished]);
    }

    /**
     * Set a reading status directly (used by the "Want to read / Currently
     * reading / Finished" picker on the book page). Works for any book,
     * not just ones with an epub file — a catalogue-only or external-link
     * book can still be marked as being read.
     * POST /books/{book}/status  { status: reading|finished|none }
     */
    public function setStatus(Request $request, Book $book)
    {
        $validated = $request->validate([
            'status' => 'required|in:reading,finished,none',
        ]);

        if ($validated['status'] === 'none') {
            ProgressBook::where('user_id', auth()->id())
                ->where('book_id', $book->book_id)
                ->delete();

            return response()->json(['status' => 'none']);
        }

        $progress = ProgressBook::firstOrCreate(
            ['user_id' => auth()->id(), 'book_id' => $book->book_id]
        );

        $progress->update([
            'is_finished' => $validated['status'] === 'finished',
        ]);

        return response()->json(['status' => $validated['status']]);
    }
}
