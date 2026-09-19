@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-lg-7 col-xl-6 mx-auto">

        <div class="card">

            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Site Settings</h4>
                        <p class="text-muted mb-0 fs-13">Customize how the admin panel looks.</p>
                    </div>
                </div>
            </div>
            <!--end card-header-->

            <div class="card-body pt-0">

                <h5 class="mt-2">Sidebar Logo</h5>
                <p class="text-muted fs-13">
                    Replace the "LibTune" wordmark in the admin sidebar with your own logo.
                    PNG, JPG, WEBP or SVG, up to 1MB.
                </p>

                <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
                    <div class="d-flex align-items-center justify-content-center bg-white rounded border" style="width: 90px; height: 60px;">
                        @if($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" alt="Current logo" style="max-width: 80px; max-height: 50px; object-fit: contain;">
                        @else
                            <span class="fw-bold text-primary">LibTune</span>
                        @endif
                    </div>
                    <div class="text-muted fs-13">
                        {{ $logoPath ? 'Custom logo currently in use.' : 'Using the default LibTune wordmark.' }}
                    </div>
                </div>

                <form action="{{ route('admin.settings.logo.update') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="mb-2">
                    @csrf

                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <div class="mb-3 mb-md-0">
                                <label for="logo" class="form-label">Upload New Logo</label>
                                <div class="input-group @error('logo') is-invalid @enderror">
                                    <label class="input-group-text" for="logo">Upload</label>
                                    <input type="file"
                                           name="logo"
                                           id="logo"
                                           accept="image/*"
                                           class="form-control @error('logo') is-invalid @enderror">
                                </div>
                                @error('logo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                Save Logo
                            </button>
                        </div>
                    </div>
                </form>

                @if($logoPath)
                    <form action="{{ route('admin.settings.logo.destroy') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-link text-danger px-0"
                                onclick="return confirmAction(this, {
                                    title: 'Remove custom logo?',
                                    text: \"You'll go back to the default logo until you upload a new one.\",
                                    icon: 'warning',
                                    confirmButtonText: 'Yes, remove it',
                                    confirmButtonClass: 'btn btn-danger'
                                })">
                            Remove custom logo
                        </button>
                    </form>
                @endif

            </div>
            <!--end card-body-->
        </div>
        <!--end card-->

    </div>
</div>

@endsection
