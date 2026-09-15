<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book; // Import your Book model
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Query database metrics or pass default values
        $totalBooks = Book::count();
        $archivedBooks = Book::where('status', 'archived')->count(); // Adjust column/condition as needed

        return view('admin.dashboard', compact('totalBooks', 'archivedBooks'));
    }
}