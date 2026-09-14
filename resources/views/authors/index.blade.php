@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4>Authors</h4>
            <p class="text-muted">Manage your library authors.</p>
        </div>

        <a href="{{ route('admin.authors.create') }}"
           class="btn btn-primary">
            Add Author
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Author</th>
                            <th>Books</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($authors as $author)

                            <tr>

                                <td>
                                    {{ $author->auth_id }}
                                </td>

                                <td>
                                    {{ $author->auth_name }}
                                </td>

                                <td>
                                    {{ $author->books_count }}
                                </td>

                                <td>

                                    <a href="{{ route('admin.authors.edit', $author) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.authors.destroy', $author) }}"
                                          method="POST"
                                          class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this author?')">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center">
                                    No authors found.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{ $authors->links() }}

        </div>
    </div>

</div>

@endsection
