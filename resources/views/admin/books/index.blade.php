@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Books</h4>
                        <p class="text-muted mb-0 fs-13">Manage the books in your e-library.</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.books.create') }}" class="btn btn-primary px-4">
                            <i class="iconoir-plus me-1"></i>
                            Add Book
                        </a>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if($books->count())

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">

                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th style="width: 70px;">Cover</th>
                                    <th>Book</th>
                                    <th>Author(s)</th>
                                    <th>Category</th>
                                    <th>Published</th>
                                    <th>Status</th>
                                    <th class="text-end" style="width: 100px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach($books as $book)

                                    <tr>

                                        <td class="text-muted">
                                            {{ $loop->iteration + ($books->currentPage() - 1) * $books->perPage() }}
                                        </td>

                                        <td>
                                            @if($book->cover_image)
                                                <img src="{{ asset('storage/' . $book->cover_image) }}"
                                                     alt="{{ $book->title }}"
                                                     class="rounded"
                                                     width="42" height="56"
                                                     style="object-fit: cover;">
                                            @else
                                                <span class="d-flex align-items-center justify-content-center rounded bg-primary-subtle text-primary"
                                                      style="width: 42px; height: 56px;">
                                                    <i class="iconoir-book fs-4"></i>
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.books.edit', $book->book_id) }}" class="text-dark fw-semibold text-decoration-none">
                                                {{ $book->title }}
                                            </a>
                                        </td>

                                        <td>
                                            @if($book->authors->count())
                                                {{ $book->authors->pluck('name')->join(', ') }}
                                            @else
                                                <span class="text-muted">No author</span>
                                            @endif
                                        </td>

                                        <td>
                                            @forelse($book->categories as $category)
                                                <span class="badge bg-info-subtle text-info">
                                                    {{ $category->cate_name }}
                                                </span>
                                            @empty
                                                <span class="text-muted">Uncategorized</span>
                                            @endforelse
                                        </td>

                                        <td>{{ $book->published_year ?? 'N/A' }}</td>

                                        <td>
                                        <form action="{{ route('admin.books.toggle-status', $book->book_id) }}" method="POST">
                                             @csrf
                                    <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" role="switch"
                                      onchange="this.form.submit()"
                                      {{ !$book->is_archived ? 'checked' : '' }}>
                                         <label class="form-check-label small {{ $book->is_archived ? 'text-secondary' : 'text-success' }}">
                                        {{ $book->is_archived ? 'Inactive' : 'Active' }}
                                        </label>
                                      </div>
                                        </form>
                                        </td>

                                        <td class="text-end">

                                            <a href="{{ route('admin.books.edit', $book->book_id) }}"
                                               class="btn btn-sm btn-light"
                                               title="Edit">
                                                <i class="iconoir-edit-pencil"></i>
                                            </a>

                                            <form action="{{ route('admin.books.destroy', $book->book_id) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                        class="btn btn-sm btn-light text-danger"
                                                        title="Delete"
                                                        onclick="return confirmDelete(this, '{{ addslashes($book->title) }}')">
                                                    <i class="iconoir-trash"></i>
                                                </button>
                                            </form>

                                        </td>

                                    </tr>

                                @endforeach

                            </tbody>

                        </table>
                    </div>
                    <!--end table-responsive-->

                    <div class="mt-3">
                        {{ $books->links('pagination::bootstrap-5') }}
                    </div>

                @else

                    <div class="text-center py-5">
                        <i class="iconoir-book-stack fs-1 text-muted mb-2 d-block"></i>
                        <h5>No books found</h5>
                        <p class="text-muted">There are currently no books in the library.</p>
                        <a href="{{ route('admin.books.create') }}" class="btn btn-primary px-4">
                            Add Your First Book
                        </a>
                    </div>

                @endif

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection
