<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalAuthors = Author::count();
        $totalCategories = BookCategory::count();
        $totalUsers = User::count();
        $archivedBooks = Book::where('is_archived', true)->count();

        // Pulled wider than one "page" of the dashboard widget on purpose —
        // the Blade view paginates these client-side in fixed-size pages so
        // the card height (and therefore the footer position) stays stable
        // no matter how many books/categories exist.
        $recentBooks = Book::with(['authors', 'category'])
            ->latest()
            ->limit(20)
            ->get();

        $topCategories = BookCategory::withCount('books')
            ->orderByDesc('books_count')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalAuthors',
            'totalCategories',
            'totalUsers',
            'archivedBooks',
            'recentBooks',
            'topCategories'
        ));
    }
}
