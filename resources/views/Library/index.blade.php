@extends('layouts.app')

@section('title', 'My Library')

@section('content')
<section class="catalogue-head">
    <p class="eyebrow">YOUR LIBRARY</p>
    <h1>My Library</h1>
    <p>{{ $books->count() }} {{ Str::plural('book', $books->count()) }} saved to your shelf.</p>
</section>

<section class="section">
    @if($books->count())
        <div class="section-head">
            <div>
                <p class="eyebrow">YOUR SAVED BOOKS</p>
                <h2>Books you've saved</h2>
            </div>
            <a class="text-link" href="{{ route('books.search') }}">
                Browse more <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="book-grid">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-bookmark"></i>
            <h3>Your library is empty.</h3>
            <p>Save a book and it will appear here.</p>
            <a href="{{ route('books.search') }}" class="button button-dark">
                Browse books
            </a>
        </div>
    @endif
</section>
@endsection
