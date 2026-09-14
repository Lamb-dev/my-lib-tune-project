@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Book Categories</h4>

                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                        <i class="iconoir-plus me-1"></i>
                        Add Category
                    </a>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($categories->count() > 0)

                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">

                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 100px;">ID</th>
                                        <th>Category Name</th>
                                        <th style="width: 200px;">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $category->cate_id }}</td>

                                            <td>
                                                {{ $category->cate_name }}
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

                    @else

                        <div class="text-center py-5">
                            <h5>No Categories Found</h5>
                            <p class="text-muted">
                                You haven't added any book categories yet.
                            </p>

                            <a href="{{ route('admin.categories.create') }}"
                               class="btn btn-primary">
                                Add Your First Category
                            </a>
                        </div>

                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
