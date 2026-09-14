@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-lg-9 col-xl-8 mx-auto">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Edit Book</h4>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                @if($book->cover_image)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $book->cover_image) }}"
                             alt="{{ $book->title }}"
                             class="img-thumbnail"
                             width="90">
                    </div>
                @endif

                <form action="{{ route('admin.books.update', $book->book_id) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Book Title</label>
                                <input type="text"
                                       name="title"
                                       id="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title', $book->title) }}"
                                       placeholder="Enter book title"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="published_year" class="form-label">Published Year</label>
                                <input type="number"
                                       name="published_year"
                                       id="published_year"
                                       class="form-control @error('published_year') is-invalid @enderror"
                                       value="{{ old('published_year', $book->published_year) }}"
                                       placeholder="e.g. 2024">
                                @error('published_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="authors" class="form-label">Author(s)</label>
                                <select name="authors[]"
                                        id="authors"
                                        class="form-select @error('authors') is-invalid @enderror"
                                        multiple
                                        required>
                                    @foreach($authors as $author)
                                        <option value="{{ $author->auth_id }}"
                                            {{ $book->authors->contains('auth_id', $author->auth_id) ? 'selected' : '' }}>
                                            {{ $author->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hold Ctrl (Cmd on Mac) to select multiple.</small>
                                @error('authors')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cate_id" class="form-label">Category</label>
                                <select name="cate_id" id="cate_id" class="form-select @error('cate_id') is-invalid @enderror">
                                    <option value="">Select a category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->cate_id }}"
                                            {{ old('cate_id', $book->cate_id) == $category->cate_id ? 'selected' : '' }}>
                                            {{ $category->cate_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('cate_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="copyright_status" class="form-label">Copyright Status</label>
                                <select name="copyright_status" id="copyright_status" class="form-select">
                                    <option value="copyrighted" {{ old('copyright_status', $book->copyright_status) == 'copyrighted' ? 'selected' : '' }}>
                                        Copyrighted
                                    </option>
                                    <option value="public_domain" {{ old('copyright_status', $book->copyright_status) == 'public_domain' ? 'selected' : '' }}>
                                        Public Domain
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cover_image" class="form-label">
                                    Cover Image
                                    <span class="text-muted fs-12">(leave empty to keep current)</span>
                                </label>
                                <div class="input-group @error('cover_image') is-invalid @enderror">
                                    <label class="input-group-text" for="cover_image">Upload</label>
                                    <input type="file"
                                           name="cover_image"
                                           id="cover_image"
                                           accept="image/*"
                                           class="form-control @error('cover_image') is-invalid @enderror">
                                </div>
                                @error('cover_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="reading_url" class="form-label">
                                    Reading URL <span class="text-muted fs-12">(optional external link)</span>
                                </label>
                                <input type="url"
                                       name="reading_url"
                                       id="reading_url"
                                       class="form-control @error('reading_url') is-invalid @enderror"
                                       value="{{ old('reading_url', $book->reading_url) }}"
                                       placeholder="https://example.com/read">
                                @error('reading_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description"
                                          id="description"
                                          class="form-control"
                                          rows="5"
                                          placeholder="Enter a short description">{{ old('description', $book->description) }}</textarea>
                            </div>
                        </div>
                    </div>
                    <!--end row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       name="is_archived" id="is_archived" value="1"
                                       {{ old('is_archived', $book->is_archived) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_archived">
                                    Archive this book
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12 text-end">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-light px-4">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                Update Book
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
