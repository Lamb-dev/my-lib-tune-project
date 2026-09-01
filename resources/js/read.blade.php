<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $book->title }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-4">
                <div id="viewer" style="height: 75vh;" class="border rounded"></div>

                <div class="flex justify-between items-center mt-4">
                    <button id="prev" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                        &lsaquo; Prev
                    </button>
                    <span id="progress-label" class="text-sm text-gray-500"></span>
                    <button id="next" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                        Next &rsaquo;
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hands the server-rendered values to reader.js without a build-time env var --}}
    <script>
        window.READER_CONFIG = {
            fileUrl: @json($fileUrl),
            progressUrl: @json($progressUrl),
            lastCfi: @json($lastCfi),
            csrfToken: @json(csrf_token()),
        };
    </script>

    @vite('resources/js/reader.js')
</x-app-layout>
