@extends('admin.layout')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Edit Book Category</h4>
                </div>

                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.categories.update', $category->cate_id) }}"
                          method="POST">

                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="cate_name" class="form-label">
                                Category Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="cate_name"
                                name="cate_name"
                                value="{{ old('cate_name', $category->cate_name) }}"
                                placeholder="Enter category name"
                                required
                            >
                        </div>

                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-primary">
                                Update Category
                            </button>

                            <a href="{{ route('admin.categories.index') }}"
                               class="btn btn-secondary">
                                Cancel
                            </a>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
