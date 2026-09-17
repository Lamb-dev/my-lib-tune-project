@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Categories</h4>
                        <p class="text-muted mb-0 fs-13">Manage the genres/categories in your library.</p>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary px-4">
                            <i class="iconoir-plus me-1"></i>
                            Add Category
                        </a>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if($categories->count())

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0">

                            <thead class="table-light">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Category Name</th>
                                    <th style="width: 90px;">Books</th>
                                    <th style="width: 170px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($categories as $category)
                                    <tr>
                                        <td>{{ $category->cate_id }}</td>

                                        <td class="align-middle">{{ $category->cate_name }}</td>

                                        <td class="align-middle">
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $category->books()->count() }}
                                            </span>
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.categories.edit', $category->cate_id) }}"
                                               class="btn btn-sm btn-warning">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.categories.destroy', $category->cate_id) }}"
                                                  method="POST"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to delete this category?')">
                                                    Delete
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
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>

                @else

                    <div class="text-center py-5">
                        <h5>No Categories Found</h5>
                        <p class="text-muted">
                            You haven't added any book categories yet.
                        </p>

                        <a href="{{ route('admin.categories.create') }}"
                           class="btn btn-primary px-4">
                            Add Your First Category
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
