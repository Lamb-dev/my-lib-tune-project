<?php
namespace App\Http\Controllers;

use Illuminate\View\View;

class SavedBookController extends Controller
{
    public function index(): View
    {
        $books = auth()->user()
            ->savedBooks()
            ->with(['authors', 'categories'])
            ->withCount('ratings')
            ->latest('saved_books.created_at')
            ->get();

        return view('library.index', compact('books'));
    }
}
