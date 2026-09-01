<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Book;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookReaderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', fn()=>redirect()->route('dashboard'));
Route::get('/dashboard', function(){
    $popular=Book::with(['author','category'])->withCount('ratings')->orderByDesc('published_year')->limit(5)->get();
    $recent=Book::with(['author','category'])->withCount('ratings')->latest()->limit(5)->get();
    return view('dashboard',compact('popular','recent'));
})->name('dashboard');

Route::get('/search',[BookController::class,'search'])->name('books.search');
Route::get('/books/{book}',function(Book $book){$book->load(['author','category']);return view('books.show',compact('book'));})->name('books.show');
Route::get('/books/{book}/reviews',[BookController::class,'getReviews'])->name('books.reviews');
Route::get('/books/{book}/read',[BookReaderController::class,'read'])->middleware('auth')->name('books.read');
Route::get('/books/{book}/file',[BookReaderController::class,'stream'])->middleware('auth')->name('books.file');
Route::post('/books/{book}/progress',[BookReaderController::class,'saveProgress'])->middleware('auth')->name('books.progress');
Route::post('/books/{book}/review',[BookController::class,'storeReview'])->middleware('auth')->name('books.review');
Route::post('/books/{book}/save',function(Book $book){$saved=auth()->user()->savedBooks()->where('book_id',$book->book_id)->exists(); if($saved){auth()->user()->savedBooks()->where('book_id',$book->book_id)->delete();$saved=false;}else{auth()->user()->savedBooks()->create(['book_id'=>$book->book_id]);$saved=true;}return response()->json(['saved'=>$saved]);})->middleware('auth')->name('books.save');

Route::middleware('guest')->group(function(){Route::get('/login',[AuthenticatedSessionController::class,'create'])->name('login');Route::post('/login',[AuthenticatedSessionController::class,'store']);Route::get('/register',[RegisteredUserController::class,'create'])->name('register');Route::post('/register',[RegisteredUserController::class,'store']);});
Route::post('/logout',[AuthenticatedSessionController::class,'destroy'])->middleware('auth')->name('logout');
Route::middleware('auth')->group(function(){Route::get('/profile',[ProfileController::class,'edit'])->name('profile.edit');Route::patch('/profile',[ProfileController::class,'update'])->name('profile.update');Route::delete('/profile',[ProfileController::class,'destroy'])->name('profile.destroy');});
