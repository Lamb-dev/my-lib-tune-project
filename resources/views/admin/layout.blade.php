<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">
<head>
    <meta charset="utf-8" />
    <title>{{ $title ?? 'LibTune Admin' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    {{-- Apply the saved admin theme before first paint, so it stays dark/light
         across every admin page instead of resetting to light on navigation. --}}
    <script>
        (function () {
            var savedTheme = localStorage.getItem('libtune-admin-theme') || 'light';
            document.documentElement.setAttribute('data-bs-theme', savedTheme);
        })();
    </script>

    <link rel="shortcut icon" href="{{ asset('backend/assets/images/favicon.ico') }}">

    <link href="{{ asset('backend/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    @yield('styles')
</head>

<body>

    <!-- Top Bar -->
    <div class="topbar d-print-none">
        <div class="container-xxl">
            <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">
                <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                    <li>
                        <button class="nav-link mobile-menu-btn nav-icon" id="togglemenu">
                            <i class="iconoir-menu-scale"></i>
                        </button>
                    </li>
                    <li class="mx-3 welcome-text">
                        <h3 class="mb-0 fw-bold text-truncate">{{ $title ?? 'LibTune Admin' }}</h3>
                    </li>
                </ul>

                <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                    <li class="me-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener">
                            <i class="iconoir-open-new-window me-1"></i>
                            View Site
                        </a>
                    </li>

                    <li class="topbar-item">
                        <a class="nav-link nav-icon" href="javascript:void(0);" id="light-dark-mode" title="Toggle dark mode">
                            <i class="icofont-moon dark-mode"></i>
                            <i class="icofont-sun light-mode"></i>
                        </a>
                    </li>

                    <li class="dropdown topbar-item">
                        <a class="nav-link dropdown-toggle arrow-none nav-icon d-flex align-items-center" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <span class="thumb-md rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold">
                                {{ strtoupper(substr(optional(auth('admin')->user())->admin_username ?? 'A', 0, 1)) }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end py-0">
                            <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                                <div class="flex-grow-1 text-truncate">
                                    <h6 class="my-0 fw-medium text-dark fs-13">
                                        {{ optional(auth('admin')->user())->admin_username ?? 'Admin' }}
                                    </h6>
                                    <small class="text-muted mb-0">Administrator</small>
                                </div>
                            </div>
                            <div class="dropdown-divider mt-0 mb-0"></div>
                            <a href="{{ route('admin.settings.edit') }}" class="dropdown-item">
                                <i class="iconoir-settings fs-18 me-1 align-text-bottom"></i> Site Settings
                            </a>
                            <div class="dropdown-divider mt-0 mb-0"></div>
                            <form action="{{ route('admin.logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <!-- end Top Bar -->

    <!-- Sidebar -->
    <div class="startbar d-print-none">
        <div class="brand">
            <a href="{{ route('admin.dashboard') }}" class="logo d-flex align-items-center">
                @php
                    $customLogo = collect(\Illuminate\Support\Facades\Storage::disk('public')->exists('branding')
                        ? \Illuminate\Support\Facades\Storage::disk('public')->files('branding')
                        : [])->first(fn ($file) => str_starts_with(basename($file), 'logo.'));
                @endphp

                @if($customLogo)
                    <img src="{{ asset('storage/' . $customLogo) }}" alt="Logo" style="height: 34px; max-width: 160px; object-fit: contain;">
                @else
                    <span class="fw-bold fs-4 text-primary">LibTune</span>
                @endif
            </a>
        </div>

        <div class="startbar-menu">
            <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
                <div class="d-flex align-items-start flex-column w-100">
                    <ul class="navbar-nav mb-auto w-100">

                        <li class="menu-label pt-0 mt-0"><span>Main Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="iconoir-home-simple menu-icon"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>

                        <li class="menu-label mt-2"><span>Library</span></li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}" href="{{ route('admin.books.index') }}">
                                <i class="iconoir-compact-disc menu-icon"></i>
                                <span>Books</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.authors.*') ? 'active' : '' }}" href="{{ route('admin.authors.index') }}">
                                <i class="iconoir-peace-hand menu-icon"></i>
                                <span>Authors</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                                <i class="iconoir-journal-page menu-icon"></i>
                                <span>Categories</span>
                            </a>
                        </li>

                        <li class="menu-label mt-2"><span>Community</span></li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                <i class="iconoir-group menu-icon"></i>
                                <span>Users</span>
                            </a>
                        </li>

                        <li class="menu-label mt-2"><span>System</span></li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
                                <i class="iconoir-settings menu-icon"></i>
                                <span>Settings</span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- end Sidebar -->

    <div class="page-wrapper">
        <div class="page-content">
            <div class="container-xxl">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')

            </div>

            {{-- The theme's CSS positions .footer with position:absolute relative
                 to .page-content (which reserves padding-bottom for it) - it has
                 to live inside .page-content, not as a sibling, or it floats over
                 whatever content happens to be at that scroll position instead of
                 sitting at the true bottom of the page. --}}
            <footer class="footer text-center text-sm-start d-print-none">
                <div class="container-xxl">
                    <div class="row">
                        <div class="col-12">
                            <p class="text-muted mb-0 py-2">&copy; {{ date('Y') }} LibTune Admin</p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/app.js') }}"></script>

    {{-- app.js already toggles the data-bs-theme attribute when #light-dark-mode
         is clicked; this just remembers the choice so it survives page loads. --}}
    <script>
        (function () {
            var toggle = document.getElementById('light-dark-mode');
            if (!toggle) return;
            toggle.addEventListener('click', function () {
                var current = document.documentElement.getAttribute('data-bs-theme');
                localStorage.setItem('libtune-admin-theme', current);
            });
        })();
    </script>

    @yield('scripts')

</body>
</html>
