@extends('admin.layout')

@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
@endphp

@section('content')

<div class="mb-4">
    <h3 class="mb-1">{{ $greeting }}, {{ optional(auth('admin')->user())->admin_username ?? 'Admin' }}!</h3>
    <p class="text-muted mb-0">Here's what's happening in your library today.</p>
</div>

{{-- Stat cards --}}
<div class="row">
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Total Books</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $totalBooks }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-primary-subtle rounded-circle">
                            <i class="iconoir-compact-disc fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
                <p class="mb-0 text-muted mt-3 fs-13">
                    <span class="text-success">{{ $totalBooks - $archivedBooks }}</span> active &middot;
                    <span class="text-secondary">{{ $archivedBooks }}</span> archived
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Total Authors</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $totalAuthors }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-success-subtle rounded-circle">
                            <i class="iconoir-peace-hand fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
                <p class="mb-0 text-muted mt-3 fs-13">Contributing to your catalog</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Categories</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $totalCategories }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-warning-subtle rounded-circle">
                            <i class="iconoir-journal-page fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
                <p class="mb-0 text-muted mt-3 fs-13">Genres available to browse</p>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="text-dark mb-0 fw-semibold fs-14">Registered Readers</p>
                        <h3 class="mt-2 mb-0 fw-bold">{{ $totalUsers }}</h3>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex justify-content-center align-items-center thumb-lg bg-info-subtle rounded-circle">
                            <i class="iconoir-group fs-3 text-info"></i>
                        </div>
                    </div>
                </div>
                <p class="mb-0 text-muted mt-3 fs-13">People with an account</p>
            </div>
        </div>
    </div>
</div>
<!--end stat cards-->

{{-- Charts --}}
<div class="row">
    <div class="col-lg-8">
        <div class="card card-h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Reading Activity</h4>
                        <p class="text-muted mb-0 fs-12">Sample trend data &mdash; not yet wired to real analytics</p>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div id="reading_activity_chart"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card card-h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Traffic Sources</h4>
                        <p class="text-muted mb-0 fs-12">Sample data &mdash; not yet wired to real analytics</p>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0 text-center">
                <div id="traffic_sources_chart"></div>
                <p class="text-muted fs-13 mt-n2 mb-0">Most readers arrive via Organic Search</p>
            </div>
        </div>
    </div>
</div>
<!--end charts-->

{{-- Tables --}}
<div class="row">
    <div class="col-lg-6">
        <div class="card card-h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Recently Added Books</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.books.index') }}" class="btn btn-sm btn-light">View all</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                @if($recentBooks->count())
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Title</th>
                                    <th class="border-top-0">Author(s)</th>
                                    <th class="border-top-0">Category</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBooks as $book)
                                    <tr class="js-page-row" data-page-group="recent-books">
                                        <td>
                                            <a href="{{ route('admin.books.edit', $book->book_id) }}" class="text-dark text-decoration-none fw-semibold">
                                                {{ $book->title }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $book->authors->pluck('name')->join(', ') ?: 'No author' }}
                                        </td>
                                        <td>
                                            {{ $book->category->cate_name ?? 'Uncategorized' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 js-pager" data-page-group="recent-books" data-per-page="5">
                        <button type="button" class="btn btn-sm btn-light js-page-prev">
                            <i class="iconoir-nav-arrow-left"></i> Prev
                        </button>
                        <span class="text-muted fs-13 js-page-label">Page 1</span>
                        <button type="button" class="btn btn-sm btn-light js-page-next">
                            Next <i class="iconoir-nav-arrow-right"></i>
                        </button>
                    </div>
                @else
                    <p class="text-muted mb-0 py-3">No books added yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card card-h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Top Categories</h4>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-light">Manage</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                @if($topCategories->count())
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-top-0">Category</th>
                                    <th class="border-top-0">Books</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCategories as $category)
                                    <tr class="js-page-row" data-page-group="top-categories">
                                        <td>{{ $category->cate_name }}</td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $category->books_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 js-pager" data-page-group="top-categories" data-per-page="5">
                        <button type="button" class="btn btn-sm btn-light js-page-prev">
                            <i class="iconoir-nav-arrow-left"></i> Prev
                        </button>
                        <span class="text-muted fs-13 js-page-label">Page 1</span>
                        <button type="button" class="btn btn-sm btn-light js-page-next">
                            Next <i class="iconoir-nav-arrow-right"></i>
                        </button>
                    </div>
                @else
                    <p class="text-muted mb-0 py-3">No categories yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
<!--end tables-->

{{-- Inbox + quick actions --}}
<div class="row">
    <div class="col-lg-7">
        <div class="card card-h-100">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title">Support Inbox</h4>
                        <p class="text-muted mb-0 fs-12">Sample preview &mdash; messaging isn't wired up yet</p>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-danger-subtle text-danger">3 new</span>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="list-group list-group-flush">

                    <div class="list-group-item d-flex align-items-start px-0">
                        <span class="thumb-md rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-semibold me-2">R</span>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 fs-13">Richard Ali</h6>
                                <small class="text-muted">2 min ago</small>
                            </div>
                            <p class="text-muted mb-0 fs-13 text-truncate">"Hi, I can't find where to resume the book I was reading..."</p>
                        </div>
                    </div>

                    <div class="list-group-item d-flex align-items-start px-0">
                        <span class="thumb-md rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center fw-semibold me-2">J</span>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 fs-13">Juan Clark</h6>
                                <small class="text-muted">40 min ago</small>
                            </div>
                            <p class="text-muted mb-0 fs-13 text-truncate">"Could you add more books from Isekai category?"</p>
                        </div>
                    </div>

                    <div class="list-group-item d-flex align-items-start px-0">
                        <span class="thumb-md rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center fw-semibold me-2">A</span>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-0 fs-13">Albert Hull</h6>
                                <small class="text-muted">1 hr ago</small>
                            </div>
                            <p class="text-muted mb-0 fs-13 text-truncate">"Thanks for fixing the cover image upload!"</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card card-h-100">
            <div class="card-header">
                <h4 class="card-title">Quick Actions</h4>
            </div>
            <div class="card-body pt-0 d-flex flex-column gap-2">
                <a href="{{ route('admin.books.create') }}" class="btn btn-light text-start d-flex align-items-center">
                    <i class="iconoir-plus fs-5 me-2 text-primary"></i> Add a new book
                </a>
                <a href="{{ route('admin.authors.create') }}" class="btn btn-light text-start d-flex align-items-center">
                    <i class="iconoir-plus fs-5 me-2 text-success"></i> Add a new author
                </a>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-light text-start d-flex align-items-center">
                    <i class="iconoir-plus fs-5 me-2 text-warning"></i> Add a new category
                </a>
                <a href="{{ route('admin.settings.edit') }}" class="btn btn-light text-start d-flex align-items-center">
                    <i class="iconoir-settings fs-5 me-2 text-info"></i> Update site logo
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    <script src="{{ asset('backend/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script>
        var activityChart = new ApexCharts(document.querySelector("#reading_activity_chart"), {
            series: [{ name: 'Books read', data: [12, 19, 14, 25, 22, 30, 28, 35, 31, 40, 38, 45] }],
            chart: { height: 280, type: 'area', toolbar: { show: false } },
            colors: ['#22c55e'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            grid: { strokeDashArray: 3, yaxis: { lines: { show: false } } },
            legend: { show: false },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05 } }
        });
        activityChart.render();

        var trafficChart = new ApexCharts(document.querySelector("#traffic_sources_chart"), {
            series: [42, 28, 18, 12],
            chart: { height: 240, type: 'donut' },
            labels: ['Organic Search', 'Direct', 'Referral', 'Social'],
            colors: ['#22c55e', '#3b82f6', '#f59e0b', '#a855f7'],
            legend: { position: 'bottom', fontSize: '12px' },
            dataLabels: { enabled: false },
            plotOptions: { pie: { donut: { size: '65%' } } }
        });
        trafficChart.render();

        // ---- Simple client-side pagination for the Books & Categories widgets ----
        // Keeps each card a fixed number of rows regardless of how much data
        // exists, so the card height (and the footer sitting below it) stays stable.
        document.querySelectorAll('.js-pager').forEach(function (pager) {
            var group = pager.getAttribute('data-page-group');
            var perPage = parseInt(pager.getAttribute('data-per-page'), 10) || 5;
            var rows = Array.prototype.slice.call(
                document.querySelectorAll('.js-page-row[data-page-group="' + group + '"]')
            );
            var totalPages = Math.max(1, Math.ceil(rows.length / perPage));
            var currentPage = 1;

            var prevBtn = pager.querySelector('.js-page-prev');
            var nextBtn = pager.querySelector('.js-page-next');
            var label = pager.querySelector('.js-page-label');

            function render() {
                rows.forEach(function (row, index) {
                    var page = Math.floor(index / perPage) + 1;
                    row.style.display = (page === currentPage) ? '' : 'none';
                });
                if (label) label.textContent = 'Page ' + currentPage + ' of ' + totalPages;
                if (prevBtn) prevBtn.disabled = currentPage <= 1;
                if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    if (currentPage > 1) { currentPage--; render(); }
                });
            }
            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    if (currentPage < totalPages) { currentPage++; render(); }
                });
            }

            render();
        });
    </script>
@endsection
