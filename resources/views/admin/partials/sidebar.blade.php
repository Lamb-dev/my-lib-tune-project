<!-- LibTune Admin Sidebar -->

<div class="left-sidebar">

    <div class="brand-logo">
        <a href="{{ route('admin.dashboard') }}">
            <span>LibTune</span>
        </a>
    </div>

    <div class="sidebar-menu">

        <div class="menu-title">
            MAIN MENU
        </div>

        <ul class="menu-list">

            <li>
                <a href="{{ route('admin.dashboard') }}">
                    <span>Dashboards</span>
                </a>
            </li>

        </ul>


        <div class="menu-title mt-4">
            ADD OPTIONS
        </div>

        <ul class="menu-list">

            <li>
                <a href="{{ route('admin.books.create') }}">
                    <span>Add Books</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <span>Add Author</span>
                    <span class="badge bg-success">New</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <span>Add Book Categories</span>
                </a>
            </li>

        </ul>

    </div>

</div>
