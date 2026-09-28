@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Team</h4>
                        <p class="text-muted mb-0 fs-13">Manage the people shown on the About Us page.</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.team.create') }}" class="btn btn-primary px-4">
                            <i class="iconoir-plus me-1"></i>
                            Add Team Member
                        </a>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">Photo</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th style="width: 90px;">Order</th>
                                <th style="width: 170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($teamMembers as $member)

                                <tr>
                                    <td>
                                        @if($member->photo_path)
                                            <img src="{{ asset('storage/' . $member->photo_path) }}"
                                                 alt="{{ $member->name }}"
                                                 class="rounded-circle"
                                                 width="40" height="40"
                                                 style="object-fit: cover;">
                                        @else
                                            <span class="thumb-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold">
                                                {{ $member->initials() }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">{{ $member->name }}</td>

                                    <td class="align-middle">{{ $member->role }}</td>

                                    <td class="align-middle">{{ $member->sort_order }}</td>

                                    <td>
                                        <a href="{{ route('admin.team.edit', $member->team_member_id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.team.destroy', $member->team_member_id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Remove {{ addslashes($member->name) }} from the team page?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        No team members yet.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>
                <!--end table-responsive-->

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection
