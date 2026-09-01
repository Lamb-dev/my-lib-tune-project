@extends('layouts.app')
@section('title', $book->title)
@section('content')
<div class="book-detail">
    <a class="back-link" href="{{ url()->previous() }}"><i class="fa-solid fa-arrow-left"></i> Back to catalogue</a>
    <div class="detail-grid">
        <div class="detail-cover-wrap"><div class="detail-cover" style="--cover: {{ ['#24344f','#536b58','#8b5e4a','#6c536f','#9a7b35'][$book->book_id % 5] }}"><span>LIB-TUNE</span><strong>{{ collect(preg_split('/\s+/',trim($book->title)))->take(2)->map(fn($w)=>strtoupper(substr($w,0,1)))->join('') }}</strong><small>{{ $book->title }}</small></div></div>
        <div class="detail-copy"><p class="eyebrow">{{ $book->category?->cate_name ?? 'BOOK' }} · {{ $book->published_year ?? '—' }}</p><h1>{{ $book->title }}</h1><p class="detail-author">by <strong>{{ $book->author?->name ?? 'Unknown author' }}</strong></p>
            <div class="detail-rating"><span class="big-rating">{{ number_format($book->averageRating(),1) }}</span><span><span class="big-stars">★★★★★</span><small>{{ $book->ratings()->count() }} reader ratings</small></span></div>
            <p class="description">{{ $book->description ?: 'No description has been added for this book yet. Open it and discover the story for yourself.' }}</p>
            <div class="detail-actions">@if($book->isReadable())<a href="{{ route('books.read',$book) }}" class="button button-dark"><i class="fa-solid fa-book-open"></i> Read online</a>@else<span class="button button-muted">Reading unavailable</span>@endif
            @auth<button class="button button-outline" data-save-book="{{ $book->book_id }}"><i class="fa-regular fa-bookmark"></i> Save</button>@endauth</div>
            <div class="book-facts"><div><span>AUTHOR</span>{{ $book->author?->name ?? 'Unknown' }}</div><div><span>YEAR</span>{{ $book->published_year ?? 'Unknown' }}</div><div><span>FORMAT</span>{{ $book->isReadable() ? 'EPUB · Online' : 'Catalogue only' }}</div></div>
        </div>
    </div>
    <section class="review-section"><div class="section-head"><div><p class="eyebrow">FROM THE COMMUNITY</p><h2>Reader ratings</h2></div></div><div id="reviews" class="reviews"><p class="muted">Loading reviews…</p></div>@auth<div class="review-box"><h3>What did you think?</h3><form id="review-form" data-review-url="{{ route('books.review',$book) }}"><div class="star-picker">@for($i=1;$i<=5;$i++)<button type="button" data-score="{{ $i }}">★</button>@endfor</div><input type="hidden" name="rating" value="5"><textarea name="review" placeholder="Share a thought about this book..." maxlength="2000"></textarea><button class="button button-dark">Post rating</button></form></div>@else<p class="signin-note"><a href="{{ route('login') }}">Sign in</a> to leave your rating.</p>@endauth</section>
</div>
@endsection
@push('scripts')<script>window.LIBTUNE_REVIEWS_URL='{{ route('books.reviews',$book) }}';</script>@endpush
