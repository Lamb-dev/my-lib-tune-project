@extends('layouts.app')
@section('title','My Library')
@section('content')

<header class="catalogue-head">
    <p class="eyebrow">YOUR SHELF</p>
    <h1>My <em>Library</em></h1>
    <p>Books you've saved to read later.</p>
</header>

<div class="section section-tight">
    @if($books->count())
        <div class="book-grid">
            @foreach($books as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>

        <div style="margin-top:45px">
            {{ $books->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-bookmark"></i>
            <h3>Your library is empty.</h3>
            <p>Save a book from its page and it'll show up here.</p>
            <a href="{{ route('books.search') }}" class="button button-dark button-small" style="margin-top:20px">Browse the catalogue</a>
        </div>
    @endif
</div>

@endsection
