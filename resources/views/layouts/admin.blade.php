<!DOCTYPE html>
<html lang="en">

<head>

    @include('admin.partials.head')

</head>

<body>

    @include('admin.partials.sidebar')

    <div class="page-wrapper">

        @include('admin.partials.navbar')

        @yield('content')

    </div>

    @include('admin.partials.scripts')

</body>

</html>
