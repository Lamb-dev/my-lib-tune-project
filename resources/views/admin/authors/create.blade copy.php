@extends('admin.layout')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h3>Add Author</h3>
        <p class="text-muted">
            Add a new author to the library.
        </p>
    </div>


    <div class="card">

        <div class="card-body">

            <form action="{{ route('admin.authors.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf


                <div class="mb-3">

                    <label class="form-label">
                        Author Name
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name') }}"
                           required>

                    @error('name')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Biography
                    </label>

                    <textarea name="biography"
                              class="form-control"
                              rows="5">{{ old('biography') }}</textarea>

                    @error('biography')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Birth Date
                    </label>

                    <input type="date"
                           name="birth_date"
                           class="form-control"
                           value="{{ old('birth_date') }}">

                    @error('birth_date')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Nationality
                    </label>

                    <input type="text"
                           name="nationality"
                           class="form-control"
                           value="{{ old('nationality') }}">

                    @error('nationality')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Author Photo
                    </label>

                    <input type="file"
                           name="photo"
                           class="form-control">

                    @error('photo')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <a href="{{ route('admin.authors.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Add Author
                </button>

            </form>

        </div>

    </div>

</div>

@endsection
