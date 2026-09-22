@extends('layouts.app')
@section('title', $author->name)
@section('content')
@php
    $photoUrl = $author->photo
        ? (str_starts_with($author->photo, 'http') ? $author->photo : asset('storage/'.$author->photo))
        : null;
@endphp

<div class="book-detail">
    <a class="back-link" href="{{ url()->previous() }}"><i class="fa-solid fa-arrow-left"></i> Back</a>

    <div class="author-detail-head detail-reveal" style="--reveal-delay:0ms">
        @if($photoUrl)
            <img class="author-detail-photo" src="{{ $photoUrl }}" alt="{{ $author->name }}">
        @else
            <div class="author-detail-photo author-detail-photo-fallback">
                {{ collect(preg_split('/\s+/', trim($author->name)))->take(2)->map(fn($w) => strtoupper(substr($w, 0, 1)))->join('') }}
            </div>
        @endif

        <div class="author-detail-copy">
            <p class="eyebrow">AUTHOR</p>
            <h1>{{ $author->name }}</h1>
            <p class="author-detail-meta">
                @if($author->nationality){{ $author->nationality }}@endif
                @if($author->nationality && $author->birth_date) · @endif
                @if($author->birth_date)Born {{ \Illuminate\Support\Carbon::parse($author->birth_date)->format('F j, Y') }}@endif
            </p>
            <p class="description">
                {{ $author->biography ?: "No biography has been added for {$author->name} yet." }}
            </p>
        </div>
    </div>

    <section class="review-section detail-reveal" style="--reveal-delay:120ms">
        <div class="section-head">
            <div>
                <p class="eyebrow">BIBLIOGRAPHY</p>
                <h2>{{ $books->count() }} {{ Str::plural('book', $books->count()) }} by {{ $author->name }}</h2>
            </div>
        </div>

        @if($books->count())
            <div class="book-grid">
                @foreach($books as $book)
                    <x-book-card :book="$book" />
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-feather-pointed"></i>
                <h3>No books yet.</h3>
                <p>Nothing by {{ $author->name }} is in the catalogue right now.</p>
            </div>
        @endif
    </section>
</div>
@endsection
