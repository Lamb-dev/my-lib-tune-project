<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="light" data-bs-theme="light">

<head>
<link rel="stylesheet" href="{{ asset('css/admin-login.css') }}">
    <meta charset="utf-8" />

    <title>LibTune | Admin Login</title>

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <meta
        content="LibTune administrator login"
        name="description"
    />

    <meta
        content=""
        name="author"
    />

    <meta
        http-equiv="X-UA-Compatible"
        content="IE=edge"
    />

    <!-- App favicon -->
    <link
        rel="shortcut icon"
        href="{{ asset('backend/assets/images/favicon.ico') }}"
    >


    <!-- App css -->
    <link
        href="{{ asset('backend/assets/css/bootstrap.min.css') }}"
        rel="stylesheet"
        type="text/css"
    />

    <link
        href="{{ asset('backend/assets/css/icons.min.css') }}"
        rel="stylesheet"
        type="text/css"
    />

    <link
        href="{{ asset('backend/assets/css/app.min.css') }}"
        rel="stylesheet"
        type="text/css"
    />

</head>


<body>

<div class="container-xxl">

    <div class="row vh-100 d-flex justify-content-center">

        <div class="col-12 align-self-center">

            <div class="card-body">

                <div class="row">

                    <div class="col-lg-4 mx-auto">

                        <div class="card">


                            <!-- Header -->

                            <div class="card-body p-0 bg-black auth-header-box rounded-top">

                                <div class="text-center p-3">

                                    <a
                                        href="{{ route('dashboard') }}"
                                        class="logo logo-admin"
                                    >

                                        <img
                                            src="{{ asset('assets/images/logo-sm.png') }}"
                                            height="50"
                                            alt="LibTune"
                                            class="auth-logo"
                                        >

                                    </a>

                                    <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">
                                        LibTune Administration
                                    </h4>

                                    <p class="text-muted fw-medium mb-0">
                                        Sign in to manage the library.
                                    </p>

                                </div>

                            </div>


                            <!-- Login Form -->

                            <div class="card-body pt-0">


                                {{-- Login errors --}}

                                @if ($errors->any())

                                    <div class="alert alert-danger mt-3">

                                        @foreach ($errors->all() as $error)

                                            <div>
                                                {{ $error }}
                                            </div>

                                        @endforeach

                                    </div>

                                @endif


                                <form
                                    class="my-4"
                                    method="POST"
                                    action="{{ route('admin.login.submit') }}"
                                >

                                    @csrf


                                    <!-- Email -->

                                    <div class="form-group mb-2">

                                        <label
                                            class="form-label"
                                            for="admin_email"
                                        >
                                            Email
                                        </label>

                                        <input
                                            type="email"
                                            class="form-control"
                                            id="admin_email"
                                            name="admin_email"
                                            value="{{ old('admin_email') }}"
                                            placeholder="Enter admin email"
                                            autocomplete="email"
                                            required
                                        >

                                    </div>


                                    <!-- Password -->

                                    <div class="form-group">

                                        <label
                                            class="form-label"
                                            for="admin_password"
                                        >
                                            Password
                                        </label>

                                        <input
                                            type="password"
                                            class="form-control"
                                            name="admin_password"
                                            id="admin_password"
                                            placeholder="Enter password"
                                            autocomplete="current-password"
                                            required
                                        >

                                    </div>


                                    <!-- Remember Me -->

                                    <div class="form-group row mt-3">

                                        <div class="col-sm-6">

                                            <div class="form-check form-switch form-switch-success">

                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="customSwitchSuccess"
                                                    name="remember"
                                                >

                                                <label
                                                    class="form-check-label"
                                                    for="customSwitchSuccess"
                                                >
                                                    Remember me
                                                </label>

                                            </div>

                                        </div>


                                        <!-- We don't have admin password recovery yet -->

                                        <div class="col-sm-6 text-end">

                                            <span class="text-muted font-13">
                                                Administrator access
                                            </span>

                                        </div>

                                    </div>


                                    <!-- Login Button -->

                                    <div class="form-group mb-0 row">

                                        <div class="col-12">

                                            <div class="d-grid mt-3">

                                                <button
                                                    class="btn btn-primary"
                                                    type="submit"
                                                >
                                                    Log In

                                                    <i class="fas fa-sign-in-alt ms-1"></i>

                                                </button>

                                            </div>

                                        </div>

                                    </div>


                                </form>


                                <!-- Return to Library -->

                                <div class="text-center mb-2">

                                    <p class="text-muted">

                                        Return to the library?

                                        <a
                                            href="{{ route('dashboard') }}"
                                            class="text-primary ms-2"
                                        >
                                            Library Home
                                        </a>

                                    </p>

                                </div>


                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- Rizz JavaScript -->

<script src="{{ asset('backend/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('backend/assets/libs/simplebar/simplebar.min.js') }}"></script>

<script src="{{ asset('backend/assets/js/app.js') }}"></script>


</body>

</html>
