<?php

use Illuminate\Support\Facades\Route;

use App\Models\Book;
use App\Services\OpenLibraryService;

use App\Http\Controllers\BookReaderController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SavedBookController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\BookRequestController as AdminBookRequestController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BookCategoryController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', function () {
    $popular = Book::where('is_archived', false)
        ->with(['authors', 'categories'])
        ->withCount('ratings')
        ->orderByDesc('published_year')
        ->limit(5)
        ->get();

    $recent = Book::where('is_archived', false)
        ->with(['authors', 'categories'])
        ->withCount('ratings')
        ->latest()
        ->limit(5)
        ->get();

    $publicDomain = Book::where('is_archived', false)
        ->where('copyright_status', 'public_domain')
        ->with(['authors', 'categories'])
        ->withCount('ratings')
        ->latest()
        ->limit(5)
        ->get();

    $continueReading = collect();
    $recentlySaved = collect();

    if (auth()->check()) {
        // Books this reader has actually opened before, most recently
        // opened first. last_read holds an epub CFI (a location marker,
        // not a percentage), so this is a "pick up where you left off"
        // list rather than a progress bar — we don't fabricate a percent
        // we can't actually measure.
        $continueReading = \App\Models\ProgressBook::where('user_id', auth()->id())
            ->whereHas('book', fn ($q) => $q->where('is_archived', false))
            ->with(['book.authors', 'book.categories'])
            ->latest('updated_at')
            ->limit(4)
            ->get()
            ->pluck('book')
            ->filter();

        $recentlySaved = auth()->user()
            ->savedBooks()
            ->where('is_archived', false)
            ->with(['authors', 'categories'])
            ->orderByPivot('created_at', 'desc')
            ->limit(6)
            ->get();
    }

    return view('dashboard', compact('popular', 'recent', 'publicDomain', 'continueReading', 'recentlySaved'));
})->name('dashboard');

Route::get('/about', function () {
    $stats = [
        'books' => Book::where('is_archived', false)->count(),
        'authors' => \App\Models\Author::count(),
        'categories' => \App\Models\BookCategory::count(),
    ];

    return view('aboutus', compact('stats'));
}) ->name('aboutus');


/*
|--------------------------------------------------------------------------
| Authentication (regular users)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| User Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/book-requests', [BookRequestController::class, 'store'])->name('book-requests.store');
});

/*
|--------------------------------------------------------------------------
| Book Catalogue / Reader
|--------------------------------------------------------------------------
*/

Route::get('/search', [BookController::class, 'search'])->name('books.search');

Route::get('/books/{book}', function (Book $book, OpenLibraryService $openLibrary) {
    // Archived means hidden from the public site entirely — including a
    // direct link someone already has bookmarked or shared, not just
    // absent from browse/search listings.
    abort_if($book->is_archived, 404);

    $book->load(['authors', 'categories']);

    if ($book->open_library_key && (! $book->description || ! $book->cate_id)) {
        $details = $openLibrary->fetchWorkDetails($book->open_library_key);

        $updates = [];
        if (! $book->description && $details['description']) {
            $updates['description'] = $details['description'];
        }
        if (! $book->cate_id) {
            $updates['cate_id'] = $openLibrary->categoryFromSubjects($details['subjects']);
        }
        if ($updates) {
            $book->update($updates);
        }
    }

    // "More by this author" — anything else sharing at least one author
    // with this book, freshest first. Skipped entirely (not queried) when
    // the book has no authors, rather than showing an empty section.
    $moreByAuthor = $book->authors->isEmpty()
        ? collect()
        : Book::with(['authors', 'categories'])
            ->whereHas('authors', fn ($q) => $q->whereIn('authors.auth_id', $book->authors->pluck('auth_id')))
            ->where('book_id', '!=', $book->book_id)
            ->where('is_archived', false)
            ->latest('book_id')
            ->limit(4)
            ->get();

    $readingStatus = null;
    if (auth()->check()) {
        $progress = \App\Models\ProgressBook::where('user_id', auth()->id())
            ->where('book_id', $book->book_id)
            ->first();

        if ($progress) {
            $readingStatus = $progress->is_finished ? 'finished' : 'reading';
        }
    }

    return view('books.show', compact('book', 'moreByAuthor', 'readingStatus'));
})->name('books.show');

Route::get('/authors/{author}', function (\App\Models\Author $author) {
    $books = $author->books()
        ->with(['authors', 'categories'])
        ->where('is_archived', false)
        ->latest('book_id')
        ->get();

    return view('authors.show', compact('author', 'books'));
})->name('authors.show');

Route::get('/books/{book}/reviews', [BookController::class, 'getReviews'])->name('books.reviews');

Route::middleware('auth')->group(function () {
    Route::get('/library', [SavedBookController::class, 'index'])->name('library.index');
    Route::get('/books/{book}/read', [BookReaderController::class, 'read'])->name('books.read');
    Route::get('/books/{book}/file', [BookReaderController::class, 'stream'])->name('books.file');
    Route::post('/books/{book}/progress', [BookReaderController::class, 'saveProgress'])->name('books.progress');
    Route::post('/books/{book}/finished', [BookReaderController::class, 'toggleFinished'])->name('books.finished');
    Route::post('/books/{book}/status', [BookReaderController::class, 'setStatus'])->name('books.status');
    Route::post('/books/{book}/review', [BookController::class, 'storeReview'])->name('books.review');
    Route::post('/books/{book}/upload-epub', [BookController::class, 'uploadEpub'])->name('books.upload-epub');
    Route::post('/books/{book}/save', function (Book $book) {
        // toggle() attaches/detaches the pivot row only — it never touches
        // the books table itself. The previous version used
        // ->where('book_id', ...)->delete() on the belongsToMany relation,
        // which operates on the *related model's* table (books), not the
        // pivot, and book_id exists on both tables — ambiguous at best,
        // and one bad day away from deleting real catalogue rows.
        $result = auth()->user()->savedBooks()->toggle($book->book_id);

        return response()->json(['saved' => ! empty($result['attached'])]);
    })->name('books.save');
});

/*
|--------------------------------------------------------------------------
| Admin Authentication (separate guard/model from regular users)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        // must be declared before the resource route, otherwise
        // books/{book} captures "lookup" as a book id
        Route::get('/books-lookup', [AdminBookController::class, 'lookup'])->name('books.lookup');
        Route::resource('books', AdminBookController::class);
        Route::resource('authors', AuthorController::class);
        Route::resource('categories', BookCategoryController::class);
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::post('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
        Route::post('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');
        Route::get('/book-requests', [AdminBookRequestController::class, 'index'])->name('book-requests.index');
        Route::post('/book-requests/{bookRequest}/approve', [AdminBookRequestController::class, 'approve'])->name('book-requests.approve');
        Route::post('/book-requests/{bookRequest}/reject', [AdminBookRequestController::class, 'reject'])->name('book-requests.reject');
        Route::delete('/book-requests/{bookRequest}', [AdminBookRequestController::class, 'destroy'])->name('book-requests.destroy');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])->name('settings.logo.update');
        Route::delete('/settings/logo', [SettingsController::class, 'destroyLogo'])->name('settings.logo.destroy');
        Route::post('/books/{book}/toggle-status', [AdminBookController::class, 'toggleStatus'])->name('books.toggle-status');
        Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    });
});
