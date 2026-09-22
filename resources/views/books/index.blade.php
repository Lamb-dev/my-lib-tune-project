@extends('layouts.app')
@section('title', $query ? 'Search · '.$query : ($publicDomainOnly ? 'Read Online Now' : 'Catalogue'))
@section('content')
<section class="catalogue-head"><p class="eyebrow">LIBRARY CATALOGUE</p><h1>{{ $query ? 'Results for “'.$query.'”' : ($publicDomainOnly ? 'Free to read, right now' : 'Explore the shelves') }}</h1><p>{{ $books->count() }} {{ Str::plural('book', $books->count()) }} in this collection.</p></section>
<div class="catalogue-layout">
    <aside class="filter-panel">
        <span class="filter-label">REFINE</span>
        <a class="filter {{ !$categoryId ? 'active' : '' }}" href="{{ route('books.search') }}">All books</a>
        @foreach($categories as $category)
            <a class="filter {{ (string) $categoryId === (string) $category->cate_id ? 'active' : '' }}" href="{{ route('books.search', ['category' => $category->cate_id]) }}">{{ $category->cate_name }}</a>
        @endforeach
        <div class="filter-rule"></div>
        <span class="filter-label">AVAILABILITY</span>
        @if($publicDomainOnly)
            <a class="filter active" href="{{ route('books.search', array_filter(['category' => $categoryId, 'query' => $query])) }}">
                <span class="read-dot"></span> Read online only <i class="fa-solid fa-xmark filter-clear"></i>
            </a>
        @else
            <a class="filter" href="{{ route('books.search', array_filter(['category' => $categoryId, 'query' => $query, 'domain' => 1])) }}">
                <span class="read-dot"></span> Read online only
            </a>
        @endif
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

<section class="book-request">
    <div class="book-request-copy">
        <p class="eyebrow">MISSING SOMETHING?</p>
        <h2>Suggest a book</h2>
        <p>Fill in what you know &mdash; an admin reviews every suggestion before it's added.</p>
    </div>
    @auth
        <form id="book-request-form" data-request-url="{{ route('book-requests.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="book-request-row">
                <div class="book-request-field">
                    <label for="req-title">Book title</label>
                    <input type="text" id="req-title" name="title" maxlength="255" required>
                </div>
                <div class="book-request-field">
                    <label for="req-author">Author</label>
                    <input type="text" id="req-author" name="author_name" maxlength="255" required>
                </div>
            </div>
            <div class="book-request-field">
                <label for="req-body">Why you'd like it <span>(optional)</span></label>
                <textarea id="req-body" name="body" maxlength="2000" rows="2"></textarea>
            </div>
            <div class="book-request-row book-request-row-end">
                <div class="book-request-field book-request-file">
                    <label for="req-cover">Cover image <span>(optional)</span></label>
                    <div class="file-picker">
                        <label for="req-cover" class="file-picker-btn"><i class="fa-solid fa-arrow-up-from-bracket"></i> Choose photo</label>
                        <span class="file-picker-name" data-file-name>No file chosen</span>
                        <input type="file" id="req-cover" name="cover_image" accept="image/png,image/jpeg,image/webp">
                    </div>
                </div>
                <button type="submit" class="button button-dark button-compact">Suggest this book</button>
            </div>
        </form>
    @else
        <a href="{{ route('login') }}" class="button button-outline">Sign in to suggest a book</a>
    @endauth
</section>
@endsection
