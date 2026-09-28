@props(['books', 'eyebrow', 'title', 'viewAllUrl', 'emptyIcon' => 'fa-regular fa-bookmark', 'emptyTitle' => 'Nothing here yet.', 'emptyBody' => '', 'perPage' => 5])
@php
    $pages = $books->chunk($perPage)->values();
@endphp

<div class="section-head">
    <div><p class="eyebrow">{{ $eyebrow }}</p><h2>{{ $title }}</h2></div>
    <a class="text-link" href="{{ $viewAllUrl }}">View all <i class="fa-solid fa-arrow-right"></i></a>
</div>

@if($books->isEmpty())
    <div class="empty-state">
        <i class="{{ $emptyIcon }}"></i>
        <h3>{{ $emptyTitle }}</h3>
        @if($emptyBody)<p>{{ $emptyBody }}</p>@endif
    </div>
@else
    <div class="book-carousel" data-book-carousel>
        @foreach($pages as $i => $pageBooks)
            <div class="book-grid" data-carousel-page="{{ $i }}" @if($i === 0) data-active @endif>
                @foreach($pageBooks as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @endforeach

        @if($pages->count() > 1)
            <button type="button" class="text-link carousel-more" data-carousel-next>
                More picks <i class="fa-solid fa-arrow-rotate-right"></i>
            </button>
        @endif
    </div>
@endif
