@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-lg-8 col-xl-6 mx-auto">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Edit Author</h4>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if($author->photo)
                    <div class="text-center mb-3">
                        <img src="{{ asset('storage/' . $author->photo) }}"
                             alt="{{ $author->name }}"
                             class="img-thumbnail rounded-circle"
                             width="100" height="100"
                             style="object-fit: cover;">
                    </div>
                @endif

                <form action="{{ route('admin.authors.update', $author->auth_id) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Author Name</label>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $author->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text"
                                       name="nationality"
                                       id="nationality"
                                       class="form-control @error('nationality') is-invalid @enderror"
                                       value="{{ old('nationality', $author->nationality) }}">
                                @error('nationality')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="birth_date" class="form-label">Birth Date</label>
                                <input type="date"
                                       name="birth_date"
                                       id="birth_date"
                                       class="form-control @error('birth_date') is-invalid @enderror"
                                       value="{{ old('birth_date', $author->birth_date) }}">
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="photo" class="form-label">
                                    Author Photo
                                    <span class="text-muted fs-12">(leave empty to keep current)</span>
                                </label>
                                <div class="input-group @error('photo') is-invalid @enderror">
                                    <label class="input-group-text" for="photo">Upload</label>
                                    <input type="file"
                                           name="photo"
                                           id="photo"
                                           accept="image/*"
                                           class="form-control @error('photo') is-invalid @enderror">
                                </div>
                                @error('photo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="biography" class="form-label">Biography</label>
                                <textarea name="biography"
                                          id="biography"
                                          class="form-control @error('biography') is-invalid @enderror"
                                          rows="5">{{ old('biography', $author->biography) }}</textarea>
                                @error('biography')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <a href="{{ route('admin.authors.index') }}" class="btn btn-light px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Update Author
                            </button>
                        </div>
                    </div>

                </form>

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection
