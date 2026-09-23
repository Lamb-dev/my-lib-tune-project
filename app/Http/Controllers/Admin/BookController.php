<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookCategory;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use App\Services\OpenLibraryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display all books.
     */
    public function index()
    {
        $books = Book::with(['authors', 'categories'])
            ->latest()
            ->paginate(10);

        return view('admin.books.index', compact('books'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create()
    {
        $authors = Author::orderBy('name')->get();

        $categories = BookCategory::orderBy('cate_name')->get();

        return view('admin.books.create', compact(
            'authors',
            'categories'
        ));
    }

    /**
     * Look up books on Open Library to pre-fill the manual add form.
     *
     * This endpoint NEVER writes to the database — it only returns data
     * for the admin to review, edit and submit through the normal
     * store() flow. Two modes:
     *   ?q=dune          -> list of matching results
     *   ?key=/works/OL1W -> description + suggested category for one work
     */
    public function lookup(Request $request, OpenLibraryService $openLibrary)
    {
        $workKey = trim((string) $request->input('key', ''));

        if ($workKey !== '') {
            $details = $openLibrary->fetchWorkDetails($workKey);

            $suggested = null;
            if (! empty($details['subjects'])) {
                $cateId = $openLibrary->categoryFromSubjects($details['subjects']);
                $suggested = BookCategory::find($cateId)?->cate_name;
            }

            return response()->json([
                'description' => $details['description'],
                'suggested_category' => $suggested,
            ]);
        }

        $query = trim((string) $request->input('q', ''));

        if ($query === '') {
            return response()->json(['results' => []]);
        }

        $results = collect($openLibrary->search($query, 8))
            ->filter(fn ($doc) => ! empty($doc['key']) && ! empty($doc['title']))
            ->map(fn ($doc) => [
                'key' => $doc['key'],
                'title' => $doc['title'],
                'authors' => $doc['author_name'] ?? [],
                'year' => $doc['first_publish_year'] ?? null,
                'cover' => $openLibrary->coverUrl($doc['cover_i'] ?? null),
                'already_added' => Book::where('open_library_key', $doc['key'])->exists(),
                'is_public_domain' => $openLibrary->isPublicDomain($doc),
                'reading_url' => $openLibrary->isPublicDomain($doc)
                    ? $openLibrary->readingUrlFromDoc($doc)
                    : null,
            ])
            ->values();

        return response()->json(['results' => $results]);
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,auth_id',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:book_categories,cate_id',
            'published_year' => 'nullable|integer|min:0|max:' . date('Y'),
            'description' => 'nullable|string',
            'copyright_status' => 'nullable|in:public_domain,copyrighted',
            'reading_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,avif,jfif|max:2048',
            'cover_image_url' => 'nullable|url|max:500',
            'open_library_key' => 'nullable|string|max:100',
            'is_archived' => 'nullable|boolean',
        ]);

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('books', 'public');
        } elseif (! empty($validated['cover_image_url'])) {
            // Filled in by the Open Library lookup. Stored as a full URL —
            // the views already handle both full URLs and local paths.
            $coverPath = $validated['cover_image_url'];
        }

        $book = Book::create([
            'title' => $validated['title'],
            // keep the legacy single-category column in sync with the pivot
            'cate_id' => $validated['categories'][0],
            'published_year' => $validated['published_year'] ?? null,
            'description' => $validated['description'] ?? null,
            'copyright_status' => $validated['copyright_status'] ?? 'copyrighted',
            'reading_url' => $validated['reading_url'] ?? null,
            'cover_image' => $coverPath,
            'open_library_key' => $validated['open_library_key'] ?? null,
            'is_archived' => $request->boolean('is_archived'),
        ]);

        $book->authors()->attach($validated['authors']);
        $book->categories()->attach($validated['categories']);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book added successfully.');
    }

    /**
     * Display a specific book.
     */
    public function show(Book $book)
    {
        $book->load([
            'authors',
            'categories',
            'ratings',
        ]);

        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing a book.
     */
    public function edit(Book $book)
    {
        $authors = Author::orderBy('name')->get();

        $categories = BookCategory::orderBy('cate_name')->get();

        $book->load(['authors', 'categories']);

        return view('admin.books.edit', compact(
            'book',
            'authors',
            'categories'
        ));
    }

    /**
     * Update a book.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'published_year' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,avif,jfif', 'max:2048'],
            'authors' => ['required', 'array', 'min:1'],
            'authors.*' => ['exists:authors,auth_id'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['exists:book_categories,cate_id'],
            'copyright_status' => ['nullable', 'in:public_domain,copyrighted'],
            'reading_url' => ['nullable', 'url', 'max:500'],
            'is_archived' => ['nullable', 'boolean'],
        ]);

        $coverPath = $book->cover_image;

        if ($request->hasFile('cover_image')) {

            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }

            $coverPath = $request->file('cover_image')->store('books', 'public');
        }

        $book->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
            'cover_image' => $coverPath,
            // keep the legacy single-category column in sync with the pivot
            'cate_id' => $validated['categories'][0],
            'copyright_status' => $validated['copyright_status'] ?? 'copyrighted',
            'reading_url' => $validated['reading_url'] ?? null,
            'is_archived' => $request->boolean('is_archived'),
        ]);

        $book->authors()->sync($validated['authors']);
        $book->categories()->sync($validated['categories']);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book updated successfully.');
    }

    /**
     * Delete a book.
     */
    public function destroy(Book $book)
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Book deleted successfully.');
    }
    public function toggleStatus(Book $book)
    {
    $book->update(['is_archived' => ! $book->is_archived]);

    return back()->with('success', '"' . $book->title . '" is now ' . ($book->is_archived ? 'inactive' : 'active') . '.');
    }
}
