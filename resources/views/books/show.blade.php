@extends('layouts.app')
@section('title', $book->title)
@section('content')
@php
    // Open Library imports store a full external URL in cover_image;
    // locally-uploaded covers store just a storage-relative path. Only
    // prepend the local storage URL when it isn't already a full URL.
    $coverUrl = $book->cover_image
        ? (str_starts_with($book->cover_image, 'http') ? $book->cover_image : asset('storage/'.$book->cover_image))
        : null;
    $isSaved = auth()->check() && in_array($book->book_id, auth()->user()->savedBookIds(), true);
@endphp
<div class="book-detail">
    <a class="back-link" href="{{ url()->previous() }}"><i class="fa-solid fa-arrow-left"></i> Back to catalogue</a>
    <div class="detail-grid">
        <div class="detail-cover-wrap detail-reveal" style="--reveal-delay:0ms">
            @if($coverUrl)
                <img class="detail-cover-img" src="{{ $coverUrl }}" alt="Cover of {{ $book->title }}">
            @else
                <div class="detail-cover" style="--cover: {{ ['#24344f','#536b58','#8b5e4a','#6c536f','#9a7b35'][$book->book_id % 5] }}"><span>LIB-TUNE</span><strong>{{ collect(preg_split('/\s+/',trim($book->title)))->take(2)->map(fn($w)=>strtoupper(substr($w,0,1)))->join('') }}</strong><small>{{ $book->title }}</small></div>
            @endif
        </div>
        <div class="detail-copy detail-reveal" style="--reveal-delay:90ms"><p class="eyebrow">{{ $book->categoryNames() }} · {{ $book->published_year ?? '—' }}</p><h1>{{ $book->title }}</h1><p class="detail-author">by <strong>{{ $book->authorNames() }}</strong></p>
            <div class="detail-rating"><span class="big-rating">{{ number_format($book->averageRating(),1) }}</span><span><span class="big-stars">★★★★★</span><small>{{ $book->ratings()->count() }} reader ratings</small></span></div>
            <p class="description">{{ $book->description ?: 'No description has been added for this book yet. Open it and discover the story for yourself.' }}</p>
            <div class="detail-actions">@if($book->isReadable())<a href="{{ route('books.read',$book) }}" class="button button-dark"><i class="fa-solid fa-book-open"></i> Read online</a>@else<span class="button button-muted">Reading unavailable</span>@endif
            @if($book->reading_url)<a href="{{ $book->reading_url }}" target="_blank" rel="noopener noreferrer" class="button button-outline"><i class="fa-solid fa-arrow-up-right-from-square"></i> View source link</a>@endif
            @auth
                <button class="button button-outline {{ $isSaved ? 'is-saved' : '' }}"
                        data-save-book="{{ $book->book_id }}"
                        aria-pressed="{{ $isSaved ? 'true' : 'false' }}">
                    <i class="fa-{{ $isSaved ? 'solid' : 'regular' }} fa-bookmark"></i> {{ $isSaved ? 'Saved' : 'Save' }}
                </button>
                <span class="save-confirm" data-save-confirm><i class="fa-solid fa-check"></i> Added to your library</span>
            @endauth</div>
            <div class="book-facts"><div><span>AUTHOR</span>{{ $book->authorNames() }}</div><div><span>YEAR</span>{{ $book->published_year ?? 'Unknown' }}</div><div><span>FORMAT</span>{{ $book->isReadable() ? 'EPUB · Online' : 'Catalogue only' }}</div></div>
        </div>
    </div>
    <section class="review-section"><div class="section-head"><div><p class="eyebrow">FROM THE COMMUNITY</p><h2>Reader ratings</h2></div></div><div id="reviews" class="reviews"><p class="muted">Loading reviews…</p></div>@auth<div class="review-box"><h3>What did you think?</h3><form id="review-form" data-review-url="{{ route('books.review',$book) }}"><div class="star-picker">@for($i=1;$i<=5;$i++)<button type="button" data-score="{{ $i }}">★</button>@endfor</div><input type="hidden" name="rating" value="5"><textarea name="review" placeholder="Share a thought about this book..." maxlength="2000"></textarea><button class="button button-dark">Post rating</button></form></div>@else<p class="signin-note"><a href="{{ route('login') }}">Sign in</a> to leave your rating.</p>@endauth</section>

    @if($moreByAuthor->count())
        <section class="review-section">
            <div class="section-head"><div><p class="eyebrow">KEEP EXPLORING</p><h2>More by {{ $book->authorNames() }}</h2></div></div>
            <div class="book-grid" data-reveal-group>@foreach($moreByAuthor as $related)<x-book-card :book="$related" />@endforeach</div>
        </section>
    @endif
</div>
@endsection
@push('scripts')<script>window.LIBTUNE_REVIEWS_URL='{{ route('books.reviews',$book) }}';</script>@endpush
