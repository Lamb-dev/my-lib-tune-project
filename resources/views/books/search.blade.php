<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 px-6">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Search Books</h1>

        <!-- Search Input -->
        <form action="{{ route('books.search') }}" method="GET" class="mb-8 flex gap-2">
            <input 
                type="text" 
                name="q" 
                value="{{ $query ?? '' }}" 
                placeholder="Search by title or author..." 
                class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                required
            >
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-lg shadow">
                Search
            </button>
        </form>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Results Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($results as $doc)
                @php
                    $coverId = $doc['cover_i'] ?? null;
                    $coverUrl = $coverId ? "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg" : 'https://via.placeholder.com/150x200?text=No+Cover';
                    $title = $doc['title'] ?? 'Unknown Title';
                    $author = $doc['author_name'][0] ?? 'Unknown Author';
                    $key = $doc['key'] ?? '';
                @endphp

                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 flex flex-col justify-between">
                    <div>
                        <img src="{{ $coverUrl }}" class="h-48 w-full object-cover rounded mb-3">
                        <h3 class="font-bold text-sm text-gray-900 truncate" title="{{ $title }}">{{ $title }}</h3>
                        <p class="text-xs text-gray-500 mb-3 truncate">{{ $author }}</p>
                    </div>

                    <!-- Shelf Selection Dropdown -->
                    <form action="{{ route('books.updateStatus') }}" method="POST">
                        @csrf
                        <input type="hidden" name="open_library_key" value="{{ $key }}">
                        <input type="hidden" name="title" value="{{ $title }}">
                        <input type="hidden" name="author" value="{{ $author }}">
                        <input type="hidden" name="cover_url" value="{{ $coverUrl }}">

                        <select name="status" onchange="this.form.submit()" class="text-xs p-2 border border-gray-300 rounded w-full bg-gray-50">
                            <option value="" selected disabled>+ Add to Shelf</option>
                            <option value="reading">Currently Reading</option>
                            <option value="want_to_read">Plan to Read</option>
                            <option value="completed">Completed</option>
                            <option value="on_hold">On Hold</option>
                            <option value="dropped">Dropped</option>
                        </select>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>