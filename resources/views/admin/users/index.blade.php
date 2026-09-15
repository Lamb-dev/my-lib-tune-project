@extends('admin.layout')

@section('content')

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

                                        <td class="text-muted">{{ $user->created_at->format('M d, Y') }}</td>
                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>
                    <!--end table-responsive-->

                    <div class="mt-3">
                        {{ $users->links() }}
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
