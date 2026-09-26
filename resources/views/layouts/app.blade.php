<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="library-url" content="{{ route('library.index') }}">
    <title>@yield('title', 'Lib-Tune') · Lib-Tune</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/libtune.css') }}">
    @stack('styles')
</head>
<body data-page="{{ Route::currentRouteName() }}">
    <div class="site-shell">
<header class="topbar">
    <a class="brand" href="{{ route('dashboard') }}" aria-label="Lib-Tune home">
        <span class="brand-mark"><i class="fa-solid fa-book-open"></i></span>
        <span>Lib<span>-</span>Tune</span>
    </a>
    <form class="global-search" action="{{ route('books.search') }}" method="GET">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input name="query" value="{{ request('query') }}" placeholder="Search books, authors..." aria-label="Search books">
        <kbd>⌘ K</kbd>
    </form>
    <nav class="top-actions">
    @auth
      <div class="nav-pill-group">
         <a href="{{ route('books.search') }}" class="nav-link {{ request()->routeIs('books.search') ? 'active' : '' }}">Browse</a>
         <a href="{{ route('library.index') }}" class="nav-link {{ request()->routeIs('library.index') ? 'active' : '' }}"><i class="fa-solid fa-bookmark"></i> My Library</a>
         <a href="{{ route('aboutus') }}" class="nav-link {{ request()->routeIs('aboutus') ? 'active' : '' }}">About</a>
     </div>
     <a href="{{ route('profile.edit') }}" class="avatar" title="Profile">
        @if(auth()->user()->profile_picture_path)
            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url(auth()->user()->profile_picture_path) }}" alt="{{ auth()->user()->username }}">
        @else
            {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
        @endif
     </a>
        <form method="POST" action="{{ route('logout') }}" class="inline-form">@csrf<button class="icon-btn" title="Sign out"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
     @else
        <div class="nav-pill-group">
            <a href="{{ route('books.search') }}" class="nav-link {{ request()->routeIs('books.search') ? 'active' : '' }}">Browse</a>
            <a href="{{ route('aboutus') }}" class="nav-link {{ request()->routeIs('aboutus') ? 'active' : '' }}">About</a>
        </div>
        <a class="nav-link" href="{{ route('login') }}">Sign in</a>
        <a class="button button-dark button-small" href="{{ route('register') }}">Join</a>
     @endauth
</nav>

</header>

        <main>
            @if(session('status'))
                <div class="toast"><i class="fa-solid fa-circle-check"></i>{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>

        <footer class="footer">
            <div class="footer-grid">
                <div class="footer-brand">
                    <span class="brand-footer"><i class="fa-solid fa-book-open"></i> Lib-Tune</span>
                    <p>A quiet place for curious readers. Browse a growing catalogue, save what you love, and read public-domain classics for free — right in your browser.</p>
                </div>

                <div class="footer-col">
                    <h4>Explore</h4>
                    <a href="{{ route('books.search') }}">Browse the catalogue</a>
                    <a href="{{ route('books.search', ['domain' => 1]) }}">Read online now</a>
                    <a href="{{ route('aboutus') }}">About us</a>
                    @auth
                        <a href="{{ route('library.index') }}">My library</a>
                    @endauth
                </div>

                <div class="footer-col">
                    <h4>Account</h4>
                    @auth
                        <a href="{{ route('profile.edit') }}">Profile settings</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline-form"><button type="submit">Sign out</button></form>
                    @else
                        <a href="{{ route('login') }}">Sign in</a>
                        <a href="{{ route('register') }}">Create an account</a>
                    @endauth
                </div>

                <div class="footer-col">
                    <h4>Have a book in mind?</h4>
                    <p class="footer-note">Suggest a title from the catalogue page and a librarian will take a look.</p>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} Lib-Tune &middot; Public-domain &amp; non-copyrighted reading</span>
                <span class="footer-heart">Built for readers, not algorithms.</span>
            </div>
        </footer>
    </div>
    <script src="{{ asset('js/libtune.js') }}"></script>
    @stack('scripts')
</body>
</html>
