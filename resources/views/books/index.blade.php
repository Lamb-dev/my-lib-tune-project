@extends('layouts.app')
@section('title', $query ? 'Search · '.$query : 'Catalogue')
@section('content')
<section class="catalogue-head"><p class="eyebrow">LIBRARY CATALOGUE</p><h1>{{ $query ? 'Results for “'.$query.'”' : 'Explore the shelves' }}</h1><p>{{ $books->count() }} {{ Str::plural('book', $books->count()) }} in this collection.</p></section>
<div class="catalogue-layout">
    <aside class="filter-panel">
        <span class="filter-label">REFINE</span>
        <a class="filter {{ !$categoryId ? 'active' : '' }}" href="{{ route('books.search') }}">All books</a>
        @foreach($categories as $category)
            <a class="filter {{ (string) $categoryId === (string) $category->cate_id ? 'active' : '' }}" href="{{ route('books.search', ['category' => $category->cate_id]) }}">{{ $category->cate_name }}</a>
        @endforeach
        <div class="filter-rule"></div>
        <span class="filter-label">AVAILABILITY</span>
        <div class="filter-note"><span class="read-dot"></span> Read online</div>
    </aside>
    <section><div class="results-toolbar"><span>Showing {{ $books->count() }} books</span><button class="view-toggle active"><i class="fa-solid fa-grid-2"></i></button></div>@if($books->count())<div class="book-grid">@foreach($books as $book)<x-book-card :book="$book" />@endforeach</div>@else<div class="empty-state"><i class="fa-solid fa-feather-pointed"></i><h3>No books found.</h3><p>{{ $suggestions->count() ? 'Nothing on our shelves matches — but these exist elsewhere.' : 'Try another title or author.' }}</p></div>@endif

    @if($suggestions->count())
        <div class="suggestions">
            <div class="suggestions-head">
                <h2>Not in our library yet</h2>
                <p>Found on Open Library. These aren't part of the collection — a librarian has to add them before you can read or save them.</p>
            </div>
            <div class="suggestion-list">
                @foreach($suggestions as $s)
                    <article class="suggestion">
                        @if($s['cover'])
                            <img src="{{ $s['cover'] }}" alt="Cover of {{ $s['title'] }}" loading="lazy">
                        @else
                            <div class="suggestion-blank"><i class="fa-solid fa-book"></i></div>
                        @endif
                        <div class="suggestion-copy">
                            <strong>{{ $s['title'] }}</strong>
                            <small>{{ $s['authors'] }}{{ $s['year'] ? ' · '.$s['year'] : '' }}</small>
                            @auth('admin')
                                <a class="suggestion-add"
                                   href="{{ route('admin.books.create', ['ol' => $s['key'], 'title' => $s['title']]) }}">
                                    <i class="fa-solid fa-plus"></i> Add to library
                                </a>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
    </section>
</div>
@endsection
