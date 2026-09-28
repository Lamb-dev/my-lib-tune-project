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

    <div class="team-grid">
        @forelse($team as $member)
            <div class="team-card">
                @if($member->photo_path)
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($member->photo_path) }}" alt="{{ $member->name }}">
                @else
                    <div class="team-card-noimg">{{ $member->initials() }}</div>
                @endif
                <div class="team-overlay">
                    <h3>{{ $member->name }}</h3>
                    <p class="team-role">{{ $member->role }}</p>
                    @if($member->bio)<p class="team-bio">{{ $member->bio }}</p>@endif
                    @if($member->email || $member->linkedin_url || $member->twitter_url || $member->website_url)
                        <div class="team-social">
                            @if($member->email)<a href="mailto:{{ $member->email }}" title="Email"><i class="fa-regular fa-envelope"></i></a>@endif
                            @if($member->linkedin_url)<a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener noreferrer" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>@endif
                            @if($member->twitter_url)<a href="{{ $member->twitter_url }}" target="_blank" rel="noopener noreferrer" title="Twitter / X"><i class="fa-brands fa-x-twitter"></i></a>@endif
                            @if($member->website_url)<a href="{{ $member->website_url }}" target="_blank" rel="noopener noreferrer" title="Website"><i class="fa-solid fa-globe"></i></a>@endif
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="muted" style="grid-column:1/-1;text-align:center">No team members added yet.</p>
        @endforelse
    </div>
</section>

@endsection
