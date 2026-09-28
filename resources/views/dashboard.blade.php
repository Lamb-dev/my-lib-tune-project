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
    <x-book-carousel-section
        :books="$popular"
        eyebrow="CURATED FOR YOU"
        title="Popular in the library"
        :view-all-url="route('books.search')"
        empty-title="The shelves are waiting."
        empty-body="Add books to begin building your library." />
</section>

<section class="quote-carousel" data-quote-carousel aria-live="polite">
    <span class="quote-mark">“</span>
    @foreach([
        ['text' => 'A reader lives a thousand lives before he dies. The man who never reads lives only one.', 'author' => 'George R. R. Martin'],
        ['text' => 'The man who does not read has no advantage over the man who cannot read.', 'author' => 'Mark Twain'],
        ['text' => 'A room without books is like a body without a soul.', 'author' => 'Cicero'],
        ['text' => 'There is no friend as loyal as a book.', 'author' => 'Ernest Hemingway'],
        ['text' => 'Books are a uniquely portable magic.', 'author' => 'Stephen King'],
    ] as $i => $quote)
        <div class="quote-slide" data-quote-slide="{{ $i }}" @if($i !== 0) hidden @endif>
            <blockquote>{{ $quote['text'] }}</blockquote>
            <cite>— {{ $quote['author'] }}</cite>
        </div>
    @endforeach
    <div class="quote-dots">
        @for($i = 0; $i < 5; $i++)
            <button type="button" class="quote-dot {{ $i === 0 ? 'active' : '' }}" data-quote-dot="{{ $i }}" aria-label="Show quote {{ $i + 1 }}"></button>
        @endfor
    </div>
</section>

<section class="section">
    <x-book-carousel-section
        :books="$recent"
        eyebrow="JUST ARRIVED"
        title="New on the shelves"
        :view-all-url="route('books.search')"
        empty-title="No new books yet." />
</section>

<section class="section section-tight">
    <x-book-carousel-section
        :books="$publicDomain"
        eyebrow="FREE TO READ"
        title="Public domain classics"
        :view-all-url="route('books.search', ['domain' => 1])"
        empty-icon="fa-solid fa-earth-americas"
        empty-title="No public-domain titles yet."
        empty-body="Copyright-free classics will show up here once added." />
</section>

<section class="discover-band"><div><p class="eyebrow">TONIGHT'S PICK</p><h2>Can't decide?<br><em>Let the shelf choose.</em></h2></div><a class="button button-light" href="{{ route('books.random') }}"><i class="fa-solid fa-shuffle"></i> Surprise me</a></section>
@endsection
