@extends('layouts.app')
@section('title','Discover')
@section('content')
<section class="hero">
    <div class="hero-copy">
        <p class="eyebrow">THE DIGITAL READING ROOM</p>
        <h1>Find a story<br><em>worth staying for.</em></h1>
        <p class="hero-sub">Explore a growing collection of books from around the world. Read freely, discover something unexpected, and leave your mark with a rating.</p>
        <form class="hero-search" action="{{ route('books.search') }}" method="GET">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input name="query" placeholder="What do you want to read?">
            <button class="button button-dark">Search</button>
        </form>
        <div class="hero-note"><span>⌁</span> Read public-domain and non-copyrighted works online</div>
    </div>
    <div class="hero-art" aria-hidden="true">
        <div class="floating-card card-back"></div><div class="floating-card card-mid"></div>
        <div class="floating-card card-front"><span>LIB-TUNE</span><strong>Read.<br>Rate.<br>Remember.</strong><small>THE READING ROOM</small></div>
    </div>
</section>

<section class="section section-tight">
    <div class="section-head"><div><p class="eyebrow">CURATED FOR YOU</p><h2>Popular in the library</h2></div><a class="text-link" href="{{ route('books.search') }}">View all <i class="fa-solid fa-arrow-right"></i></a></div>
    @if($popular->count())<div class="book-grid">@foreach($popular as $book)<x-book-card :book="$book" />@endforeach</div>
    @else <div class="empty-state"><i class="fa-regular fa-bookmark"></i><h3>The shelves are waiting.</h3><p>Add books to begin building your library.</p></div>@endif
</section>

<section class="quote-band"><span class="quote-mark">“</span><blockquote>A reader lives a thousand lives before he dies. The man who never reads lives only one.</blockquote><cite>— George R. R. Martin</cite></section>

<section class="section">
    <div class="section-head"><div><p class="eyebrow">JUST ARRIVED</p><h2>New on the shelves</h2></div><a class="text-link" href="{{ route('books.search') }}">Browse catalogue <i class="fa-solid fa-arrow-right"></i></a></div>
    @if($recent->count())<div class="book-grid">@foreach($recent as $book)<x-book-card :book="$book" />@endforeach</div>
    @else <div class="empty-state"><h3>No new books yet.</h3></div>@endif
</section>

<section class="discover-band"><div><p class="eyebrow">A LIBRARY, NOT A FEED</p><h2>Choose your next<br><em>world to enter.</em></h2></div><a class="button button-light" href="{{ route('books.search') }}">Explore the collection</a></section>
@endsection
