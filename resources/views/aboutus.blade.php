@extends('layouts.app')

@section('title', 'About Us')

@section('content')

<header class="about-hero">
    <p class="eyebrow">OUR STORY</p>
    <h1>About <em>Lib&#8209;Tune</em></h1>
    <p>
        Lib-Tune started as a small side project with one goal: make it easy to find and read
        public-domain and non-copyrighted books without ads, paywalls, or a cluttered interface.
        No subscriptions, no tracking your reading habits for marketing &mdash; just a clean shelf,
        a reader that remembers your place, and a catalogue that keeps growing.
    </p>
</header>

<div class="stat-grid">
    <div>
        <strong>{{ $stats['books'] }}</strong>
        <span>Books in the Collection</span>
    </div>
    <div>
        <strong>{{ $stats['authors'] }}</strong>
        <span>Authors Represented</span>
    </div>
    <div>
        <strong>{{ $stats['categories'] }}</strong>
        <span>Categories to Explore</span>
    </div>
</div>

<section class="section-tight">
    <div class="section-head" style="justify-content:center;text-align:center;display:block">
        <h2>What you can do here</h2>
    </div>

    <div class="feature-grid">
        <div class="feature-card">
            <i class="fa-solid fa-book-open"></i>
            <h3>Read, distraction-free</h3>
            <p>Every book opens in a built-in reader that picks up right where you left off &mdash; no downloads, no extra apps.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-bookmark"></i>
            <h3>Build your own shelf</h3>
            <p>Save anything that catches your eye to My Library and come back to it whenever you're ready.</p>
        </div>
        <div class="feature-card">
            <i class="fa-solid fa-star"></i>
            <h3>Rate and review</h3>
            <p>Leave a rating and a few words on books you've finished, and see what other readers thought too.</p>
        </div>
    </div>
</section>

<section class="section-tight">
    <div class="section-head" style="justify-content:center;text-align:center;display:block">
        <h2>Our Team</h2>
        <p class="hero-sub" style="margin:14px auto 0;text-align:center">A small team keeping the shelves organized and the reader running smoothly.</p>
    </div>

@php
    // Drop a photo into public/images/team/ using the exact filename
    // below and it replaces the initials automatically — no template
    // changes needed. Recommended: a square image, at least 200x200px.
    $team = [
        [
            'name' => 'Pich chansereysophea', 'role' => 'Founder', 'initials' => 'PC',
            'email' => 'jane@example.com', 'photo' => 'images/team/jane-doe.jpg',
            'bio' => 'Started Lib-Tune after one too many library apps buried in ads. Handles the roadmap and the catalogue.',
        ],
        [
            'name' => 'Sarin Sereisatha', 'role' => 'Front-End Developer', 'initials' => 'SS',
            'email' => 'mike@example.com', 'photo' => 'images/team/mike-ross.jpg',
            'bio' => 'Designs the reading experience — covers, layout, and the little details that make a page feel calm.',
        ],
        [
            'name' => 'Rattana Sambath Ratanak', 'role' => 'Backend Developer', 'initials' => 'RSR',
            'email' => 'john@example.com', 'photo' => 'images/team/john-doe.jpg',
            'bio' => "Works on the reader and the admin tools, and hunts down whatever's making the site feel clunky.",
        ],
    ];
@endphp

<div class="team-grid">
    @foreach($team as $member)
        <div class="team-card">
            <div class="team-avatar">
                @if(file_exists(public_path($member['photo'])))
                    <img src="{{ asset($member['photo']) }}" alt="{{ $member['name'] }}">
                @else
                    {{ $member['initials'] }}
                @endif
            </div>
            <h3>{{ $member['name'] }}</h3>
            <p class="team-role">{{ $member['role'] }}</p>
            <p>{{ $member['bio'] }}</p>
            <a href="mailto:{{ $member['email'] }}" class="button button-outline button-small button-full">Contact</a>
        </div>
    @endforeach
</div>
</section>

@endsection
