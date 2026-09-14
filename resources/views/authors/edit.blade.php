@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="mb-3">
        <h4>Edit Author</h4>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.authors.update', $author) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">

                    <label class="form-label">
                        Author Name
                    </label>

                    <input type="text"
                           name="auth_name"
                           class="form-control"
                           value="{{ old('auth_name', $author->auth_name) }}"
                           required>

                </div>

                <button type="submit"
                        class="btn btn-primary">
                    Update Author
                </button>

                <a href="{{ route('admin.authors.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

@endsection
