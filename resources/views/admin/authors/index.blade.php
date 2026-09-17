@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Authors</h4>
                        <p class="text-muted mb-0 fs-13">Manage the authors in your library.</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.authors.create') }}" class="btn btn-primary px-4">
                            <i class="iconoir-plus me-1"></i>
                            Add Author
                        </a>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">

                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px;">ID</th>
                                <th style="width: 70px;">Photo</th>
                                <th>Name</th>
                                <th>Nationality</th>
                                <th style="width: 90px;">Books</th>
                                <th style="width: 170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($authors as $author)

                                <tr>
                                    <td>{{ $author->auth_id }}</td>

                                    <td>
                                        @if($author->photo)
                                            <img src="{{ asset('storage/' . $author->photo) }}"
                                                 alt="{{ $author->name }}"
                                                 class="rounded-circle"
                                                 width="40" height="40"
                                                 style="object-fit: cover;">
                                        @else
                                            <span class="thumb-sm rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold">
                                                {{ strtoupper(substr($author->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">{{ $author->name }}</td>

                                    <td class="align-middle">{{ $author->nationality ?? 'N/A' }}</td>

                                    <td class="align-middle">
                                        <span class="badge bg-primary-subtle text-primary">
                                            {{ $author->books_count }}
                                        </span>
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.authors.edit', $author->auth_id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.authors.destroy', $author->auth_id) }}"
                                              method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this author?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No authors found.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>
                <!--end table-responsive-->

                <div class="mt-3">
                    {{ $authors->links('pagination::bootstrap-5') }}
                </div>

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection
