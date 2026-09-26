@extends('layouts.app')

@section('title', 'My Profile')

@section('content')

<section class="profile-head">
    <div class="profile-avatar">
        @if($user->profile_picture_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->profile_picture_path) }}" alt="{{ $user->username }}">
        @else
            {{ strtoupper(substr($user->username, 0, 1)) }}
        @endif
    </div>
    <div>
        <p class="eyebrow">YOUR READING ROOM</p>
        <h1>{{ $user->username }}</h1>
        <p>{{ $user->email }}</p>
    </div>
</section>

<section class="section profile-section">

    <div class="section-head">
        <div>
            <p class="eyebrow">ACCOUNT</p>
            <h2>Profile settings</h2>
        </div>
    </div>

    @if(session('status') === 'profile-updated')
        <p class="signin-note" style="color:var(--sage);margin-bottom:18px">Profile updated.</p>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="profile-form">
        @csrf
        @method('PATCH')

        <label>Username
            <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
            @error('username')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label>Email
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label>Bio
            <textarea name="bio" rows="4" style="display:block;width:100%;margin-top:7px;border:1px solid var(--line);background:#fffdf8;padding:12px;outline:0;resize:vertical">{{ old('bio', $user->bio) }}</textarea>
            @error('bio')<span class="error">{{ $message }}</span>@enderror
        </label>

        <label>Profile picture
            <input type="file" name="profile_picture" accept="image/*">
            @error('profile_picture')<span class="error">{{ $message }}</span>@enderror
        </label>

        <button type="submit" class="button button-dark">Save changes</button>
    </form>

</section>

<section class="section profile-section section-tight">
    <div class="section-head">
        <div>
            <p class="eyebrow">YOUR COLLECTION</p>
            <h2>Currently reading</h2>
        </div>
        <span class="stat-pill">{{ $currentlyReading->count() }} {{ Str::plural('book', $currentlyReading->count()) }}</span>
    </div>

    @if($currentlyReading->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-book-open"></i>
            <h3>Nothing in progress.</h3>
            <p>Open a saved book to start reading.</p>
        </div>
    @else
        <div class="book-grid">
            @foreach($currentlyReading as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
    @endif
</section>

<section class="section profile-section section-tight">
    <div class="section-head">
        <div>
            <p class="eyebrow">SAVED FOR LATER</p>
            <h2>Want to read</h2>
        </div>
        <span class="stat-pill">{{ $wantToRead->count() }} {{ Str::plural('book', $wantToRead->count()) }}</span>
    </div>

    @if($wantToRead->isEmpty())
        <div class="empty-state">
            <i class="fa-regular fa-bookmark"></i>
            <h3>Nothing waiting on this shelf.</h3>
            <p>Save a book from the catalogue to see it here.</p>
        </div>
    @else
        <div class="book-grid">
            @foreach($wantToRead as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
    @endif
</section>

<section class="section profile-section section-tight">
    <div class="section-head">
        <div>
            <p class="eyebrow">YOUR SHELF</p>
            <h2>All saved books</h2>
        </div>
        <span class="stat-pill">{{ $savedBooks->count() }} {{ Str::plural('book', $savedBooks->count()) }}</span>
    </div>

    @if($savedBooks->isEmpty())
        <div class="empty-state">
            <i class="fa-regular fa-bookmark"></i>
            <h3>Your library is empty.</h3>
            <p>Save a book from the catalogue and it will appear here.</p>
        </div>
    @else
        <div class="book-grid">
            @foreach($savedBooks as $book)
                <x-book-card :book="$book" />
            @endforeach
        </div>
    @endif
</section>

@endsection
