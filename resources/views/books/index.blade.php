@extends('layouts.app')
@section('title', $query ? 'Search · '.$query : 'Catalogue')
@section('content')
<section class="catalogue-head"><p class="eyebrow">LIBRARY CATALOGUE</p><h1>{{ $query ? 'Results for “'.$query.'”' : 'Explore the shelves' }}</h1><p>{{ $books->count() }} {{ Str::plural('book', $books->count()) }} in this collection.</p></section>
<div class="catalogue-layout">
    <aside class="filter-panel"><span class="filter-label">REFINE</span><a class="filter active" href="{{ route('books.search') }}">All books</a><a class="filter" href="{{ route('books.search', ['query'=>'classic']) }}">Classics</a><a class="filter" href="{{ route('books.search', ['query'=>'romance']) }}">Romance</a><a class="filter" href="{{ route('books.search', ['query'=>'fiction']) }}">Fiction</a><a class="filter" href="{{ route('books.search', ['query'=>'history']) }}">History</a><div class="filter-rule"></div><span class="filter-label">AVAILABILITY</span><div class="filter-note"><span class="read-dot"></span> Read online</div></aside>
    <section><div class="results-toolbar"><span>Showing {{ $books->count() }} books</span><button class="view-toggle active"><i class="fa-solid fa-grid-2"></i></button></div>@if($books->count())<div class="book-grid">@foreach($books as $book)<x-book-card :book="$book" />@endforeach</div>@else<div class="empty-state"><i class="fa-solid fa-feather-pointed"></i><h3>No books found.</h3><p>Try another title or author.</p></div>@endif</section>
</div>
@endsection
