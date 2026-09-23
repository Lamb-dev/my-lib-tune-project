@extends('admin.layout')

@section('content')

@if(session('new_admin_password'))
    <div class="alert alert-warning border-warning d-flex align-items-start gap-3 mb-4" role="alert">
        <i class="iconoir-shield-alert fs-3 text-warning mt-1"></i>
        <div>
            <h5 class="alert-heading mb-1">New admin password — shown once</h5>
            <p class="mb-2">
                This is only ever displayed here, right now. It isn't emailed, logged, or stored anywhere
                in readable form — if you navigate away without copying it, it's gone for good and you'll
                need to reset it instead.
            </p>
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <div><span class="text-muted small">Admin login email</span><br><code>{{ session('new_admin_email') }}</code></div>
                <div><span class="text-muted small">Temporary password</span><br><code class="fs-5">{{ session('new_admin_password') }}</code></div>
            </div>
            <p class="small text-muted mb-0 mt-2">
                Hand this to them directly (in person, or over a channel you already trust) and have them
                change it the moment they log in.
            </p>
        </div>
    </div>
@endif

<div class="mb-4">
    <h3 class="mb-1">Users</h3>
    <p class="text-muted mb-0">Everyone who has registered a reader account.</p>
</div>

{{-- Stat cards --}}
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Total Users</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $totalUsers }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-info-subtle rounded-circle">
                            <i class="iconoir-group fs-3 text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Admins</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $totalAdmins }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-primary-subtle rounded-circle">
                            <i class="iconoir-shield-check fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">New This Month</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $newThisMonth }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-success-subtle rounded-circle">
                            <i class="iconoir-user-plus fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--end stat cards-->

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <h4 class="card-title">All Users</h4>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if($users->count())

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Ratings</th>
                                    <th>Saved Books</th>
                                    <th>Joined</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($users as $user)

                                    <tr>
                                        <td class="text-muted">
                                            {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="thumb-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold me-2">
                                                    {{ strtoupper(substr($user->username, 0, 1)) }}
                                                </span>
                                                <span class="fw-semibold">{{ $user->username }}</span>
                                            </div>
                                        </td>

                                        <td>{{ $user->email }}</td>

                                        <td>
                                            @if($user->role === 'admin')
                                                <span class="badge bg-primary-subtle text-primary">Admin</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">Reader</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge bg-warning-subtle text-warning">
                                                {{ $user->ratings_count }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-info-subtle text-info">
                                                {{ $user->saved_books_count }}
                                            </span>
                                        </td>
                                        <td>
                                        <form action="{{ route('admin.users.toggle-status', $user->user_id) }}" method="POST">
                                        @csrf
                                            <div class="form-check form-switch mb-0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                        onchange="this.form.submit()"
                                        {{ $user->is_active ? 'checked' : '' }}>
                                        <label class="form-check-label small {{ $user->is_active ? 'text-success' : 'text-secondary' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                        </div>
                                            </form>
                                        </td>
                                        <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>

                                        <td class="text-end">
                                            @if($user->role === 'admin')
                                                <form action="{{ route('admin.users.demote', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="return confirmAction(this, {
                                                                title: 'Revoke admin access?',
                                                                text: '{{ addslashes($user->username) }} will keep their reader account and everything in it \u2014 they just lose access to the admin panel.',
                                                                icon: 'warning',
                                                                confirmButtonText: 'Yes, revoke access',
                                                                confirmButtonClass: 'btn btn-danger'
                                                            })">
                                                        Revoke admin
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.users.promote', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-primary"
                                                            onclick="return confirmAction(this, {
                                                                title: 'Grant full admin access?',
                                                                text: '{{ addslashes($user->username) }} will be able to add, edit, and delete any book, author, or category, and manage other users\' access \u2014 the same level of control you have. A separate, random admin password will be generated and shown to you once on the next screen.',
                                                                icon: 'warning',
                                                                confirmButtonText: 'Yes, make admin',
                                                                confirmButtonClass: 'btn btn-primary'
                                                            })">
                                                        Make admin
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>
                    <!--end table-responsive-->

                    <div class="mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>

                @else

                    <div class="text-center py-5">
                        <i class="iconoir-group fs-1 text-muted mb-2 d-block"></i>
                        <h5>No users yet</h5>
                        <p class="text-muted">Registered readers will show up here.</p>
                    </div>

                @endif

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection
