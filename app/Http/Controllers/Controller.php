<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/search', ['App\Http\Controllers\BookController', 'search'])->name('books.search');
    Route::post('/books/status', ['App\Http\Controllers\BookController', 'updateStatus'])->name('books.updateStatus');
});


abstract class Controller
{
    // Base Controller Class
}