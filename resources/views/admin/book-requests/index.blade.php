@extends('admin.layout')

@section('content')

<div class="mb-4">
    <h3 class="mb-1">Book Requests</h3>
    <p class="text-muted mb-0">Books readers have suggested adding to the catalogue.</p>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Awaiting Review</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $pendingCount }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-warning-subtle rounded-circle">
                            <i class="iconoir-message-text fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h4 class="card-title">All Requests</h4>
    </div>
    <!--end card-header-->

    <div class="card-body pt-0">

        @if($requests->count())

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">#</th>
                            <th style="width: 60px;">Cover</th>
                            <th>Suggested Title</th>
                            <th>Author</th>
                            <th>Note</th>
                            <th>Requested By</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($requests as $request)

                            <tr>
                                <td class="text-muted">
                                    {{ $loop->iteration + ($requests->currentPage() - 1) * $requests->perPage() }}
                                </td>

                                <td>
                                    @if($request->cover_image)
                                        <img src="{{ asset('storage/'.$request->cover_image) }}"
                                             alt="Suggested cover for {{ $request->title }}"
                                             class="rounded"
                                             style="width:38px;height:56px;object-fit:cover;">
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                <td class="fw-semibold">{{ $request->title }}</td>

                                <td class="text-muted">{{ $request->author_name ?: '—' }}</td>

                                <td class="text-muted" style="max-width: 280px;">
                                    {{ $request->body ?: '—' }}
                                </td>

                                <td>{{ $request->user?->username ?? 'Deleted account' }}</td>

                                <td>
                                    @if($request->status === 'approved')
                                        <span class="badge bg-success-subtle text-success">Approved</span>
                                    @elseif($request->status === 'rejected')
                                        <span class="badge bg-danger-subtle text-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">Pending</span>
                                    @endif
                                </td>

                                <td class="text-muted">{{ $request->created_at->format('M d, Y') }}</td>

                                <td class="text-end">
                                    @if($request->status !== 'approved')
                                        <form action="{{ route('admin.book-requests.approve', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success">Approve</button>
                                        </form>
                                    @endif
                                    @if($request->status !== 'rejected')
                                        <form action="{{ route('admin.book-requests.reject', $request) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.book-requests.destroy', $request) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-light text-danger"
                                                onclick="return confirmAction(this, {
                                                    title: 'Delete this suggestion?',
                                                    text: 'This removes it permanently, including its cover image if one was attached.',
                                                    icon: 'warning',
                                                    confirmButtonText: 'Yes, delete it',
                                                    confirmButtonClass: 'btn btn-danger'
                                                })">
                                            <i class="iconoir-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $requests->links() }}
            </div>

        @else

            <div class="text-center py-5">
                <i class="iconoir-message-text fs-1 text-muted"></i>
                <h5 class="mt-3">No suggestions yet</h5>
                <p class="text-muted">When a reader suggests a book on the site, it'll show up here.</p>
            </div>

        @endif

    </div>
    <!--end card-body-->
</div>
<!--end card-->

@endsection
