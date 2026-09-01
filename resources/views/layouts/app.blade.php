<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lib-Tune') · Lib-Tune</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Libre+Baskerville:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/libtune.css') }}">
    @stack('styles')
</head>
<body>
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
                <a href="{{ route('books.search') }}" class="nav-link">Browse</a>
                @auth
                    <a href="{{ route('profile.edit') }}" class="avatar" title="Profile">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline-form">@csrf<button class="icon-btn" title="Sign out"><i class="fa-solid fa-arrow-right-from-bracket"></i></button></form>
                @else
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
            <div><span class="brand-footer">Lib-Tune</span><span> A quiet place for curious readers.</span></div>
            <div>Public-domain & non-copyrighted reading · {{ date('Y') }}</div>
        </footer>
    </div>
    <script src="{{ asset('js/libtune.js') }}"></script>
    @stack('scripts')
</body>
</html>
