<nav class="navbar">
    <div class="container-fluid">

        <a href="{{ route('admin.dashboard') }}">
            LibTune Admin
        </a>

        <div>
            <span>
                {{ auth()->user()->name ?? 'Admin' }}
            </span>

            <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
                @csrf

                <button type="submit">
                    Logout
                </button>
            </form>
        </div>

    </div>
</nav>
