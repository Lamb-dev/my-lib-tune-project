<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Models\Book;

use App\Http\Controllers\BookReaderController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;

use App\Http\Controllers\Admin\BookCategoryController;
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::get('/dashboard', function () {

    $popular = Book::with(['authors', 'category'])
        ->withCount('ratings')
        ->orderByDesc('published_year')
        ->limit(5)
        ->get();

    $recent = Book::with(['authors', 'category'])
        ->withCount('ratings')
        ->latest()
        ->limit(5)
        ->get();

    return view('dashboard', compact('popular', 'recent'));

})->name('dashboard');


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/login', [
        AuthenticatedSessionController::class,
        'create'
    ])->name('login');

    Route::post('/login', [
        AuthenticatedSessionController::class,
        'store'
    ]);

    Route::get('/register', [
        RegisteredUserController::class,
        'create'
    ])->name('register');

    Route::post('/register', [
        RegisteredUserController::class,
        'store'
    ]);

});


Route::post('/logout', [
    AuthenticatedSessionController::class,
    'destroy'
])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| User Profile
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [
        ProfileController::class,
        'edit'
    ])->name('profile.edit');

    Route::patch('/profile', [
        ProfileController::class,
        'update'
    ])->name('profile.update');

    Route::delete('/profile', [
        ProfileController::class,
        'destroy'
    ])->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Book Reader
|--------------------------------------------------------------------------
|
| These routes use BookReaderController, which already exists
| in your project.
|
*/

Route::middleware('auth')->group(function () {

    Route::get('/books/{book}/read', [
        BookReaderController::class,
        'read'
    ])->name('books.read');

    Route::get('/books/{book}/file', [
        BookReaderController::class,
        'stream'
    ])->name('books.file');

    Route::post('/books/{book}/progress', [
        BookReaderController::class,
        'saveProgress'
    ])->name('books.progress');
    Route::get('/search', [
    BookController::class,
    'search'
])->name('books.search');
});


/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        | Admin Login
        */

        Route::get('/login', [
            AdminAuthController::class,
            'showLogin'
        ])->name('login');


        Route::post('/login', [
            AdminAuthController::class,
            'login'
        ])->name('login.submit');


        Route::post('/logout', [
            AdminAuthController::class,
            'logout'
        ])->name('logout');


        /*
        | Protected Admin Pages
        */

        Route::middleware('admin')->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('books', AdminBookController::class);

            Route::resource('authors', AuthorController::class);

            Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');
            Route::resource('categories', BookCategoryController::class);

            Route::get('/settings', [SettingsController::class, 'edit'])
                ->name('settings.edit');
            Route::post('/settings/logo', [SettingsController::class, 'updateLogo'])
                ->name('settings.logo.update');
            Route::delete('/settings/logo', [SettingsController::class, 'destroyLogo'])
                ->name('settings.logo.destroy');
        });

    });
/*
|--------------------------------------------------------------------------
| Book Catalogue
|--------------------------------------------------------------------------
*/

Route::get('/search', [
    BookController::class,
    'search'
])->name('books.search');


Route::get('/books/{book}', function (Book $book) {

    $book->load([
        'authors',
        'category'
    ]);

    return view('books.show', compact('book'));

})->name('books.show');


Route::get('/books/{book}/reviews', [
    BookController::class,
    'getReviews'
])->name('books.reviews');


Route::post('/books/{book}/review', [
    BookController::class,
    'storeReview'
])->middleware('auth')->name('books.review');


Route::post('/books/{book}/save', function (Book $book) {

    $saved = auth()->user()
        ->savedBooks()
        ->where('book_id', $book->book_id)
        ->exists();

    if ($saved) {

        auth()->user()
            ->savedBooks()
            ->where('book_id', $book->book_id)
            ->delete();

        $saved = false;

    } else {

        auth()->user()
            ->savedBooks()
            ->create([
                'book_id' => $book->book_id
            ]);

        $saved = true;
    }

    return response()->json([
        'saved' => $saved
    ]);

})->middleware('auth')->name('books.save');


Route::get('/', fn()=>redirect()->route('dashboard'));
Route::get('/dashboard', function(){
    $popular=Book::with(['authors','category'])->withCount('ratings')->orderByDesc('published_year')->limit(5)->get();
    $recent=Book::with(['authors','category'])->withCount('ratings')->latest()->limit(5)->get();
    return view('dashboard',compact('popular','recent'));
})->name('dashboard');

Route::get('/search',[BookController::class,'search'])->name('books.search');
Route::get('/books/{book}', function (Book $book, \App\Services\OpenLibraryService $openLibrary) {
    $book->load(['author', 'category']);

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

    return view('books.show', compact('book'));
})->name('books.show');
Route::get('/books/{book}/reviews',[BookController::class,'getReviews'])->name('books.reviews');
Route::get('/books/{book}/read',[BookReaderController::class,'read'])->middleware('auth')->name('books.read');
Route::get('/books/{book}/file',[BookReaderController::class,'stream'])->middleware('auth')->name('books.file');
Route::post('/books/{book}/progress',[BookReaderController::class,'saveProgress'])->middleware('auth')->name('books.progress');
Route::post('/books/{book}/review',[BookController::class,'storeReview'])->middleware('auth')->name('books.review');
Route::post('/books/{book}/save',function(Book $book){$saved=auth()->user()->savedBooks()->where('book_id',$book->book_id)->exists(); if($saved){auth()->user()->savedBooks()->where('book_id',$book->book_id)->delete();$saved=false;}else{auth()->user()->savedBooks()->create(['book_id'=>$book->book_id]);$saved=true;}return response()->json(['saved'=>$saved]);})->middleware('auth')->name('books.save');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', function () {
        return 'Welcome, admin — this page only admins can reach.';
    })->name('admin.dashboard');
});
Route::post('/logout',[AuthenticatedSessionController::class,'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');Route::patch('/profile',[ProfileController::class,'update'])->name('profile.update');Route::delete('/profile',[ProfileController::class,'destroy'])->name('profile.destroy');});
