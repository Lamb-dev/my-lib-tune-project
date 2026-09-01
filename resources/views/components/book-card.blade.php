@props(['book'])
@php
    $colors = ['#24344f','#536b58','#8b5e4a','#6c536f','#9a7b35','#405d67'];
    $color = $colors[$book->book_id % count($colors)];
    $initials = collect(preg_split('/\s+/', trim($book->title)))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->join('');
@endphp
<article class="book-card">
    <a class="book-cover" href="{{ route('books.show', $book) }}" style="--cover: {{ $color }}">
        @if($book->cover_image)
            <img src="{{ asset('storage/'.$book->cover_image) }}" alt="Cover of {{ $book->title }}">
        @else
            <span class="cover-kicker">LIB-TUNE</span>
            <strong>{{ $initials }}</strong>
            <span class="cover-title">{{ $book->title }}</span>
        @endif
        <span class="cover-hover">View book <i class="fa-solid fa-arrow-right"></i></span>
    </a>
    <div class="book-meta">
        <div class="book-title-row">
            <a href="{{ route('books.show', $book) }}" class="book-title">{{ $book->title }}</a>
            @if($book->isReadable()) <span class="read-dot" title="Available to read"></span> @endif
        </div>
        <div class="author">{{ $book->author?->name ?? 'Unknown author' }}</div>
        <div class="rating-line"><span class="stars">★</span> {{ number_format($book->averageRating(), 1) }} <span class="muted">· {{ $book->ratings_count ?? $book->ratings()->count() }} ratings</span></div>
    </div>
</article>
