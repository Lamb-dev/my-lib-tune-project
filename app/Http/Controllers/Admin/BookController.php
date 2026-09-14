<?php

namespace App\Http\Controllers\Admin;

use App\Models\BookCategory;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display all books.
     */
    public function index()
    {
        $books = Book::with(['authors', 'category'])
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
     * Store a newly created book.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'authors' => 'required|array|min:1',
            'authors.*' => 'exists:authors,auth_id',
            'cate_id' => 'nullable|exists:book_categories,cate_id',
            'published_year' => 'nullable|integer|min:0|max:' . date('Y'),
            'description' => 'nullable|string',
            'copyright_status' => 'nullable|in:public_domain,copyrighted',
            'reading_url' => 'nullable|url|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_archived' => 'nullable|boolean',
        ]);

        $coverPath = null;

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('books', 'public');
        }

        $book = Book::create([
            'title' => $validated['title'],
            // legacy single-author column still required by the books table;
            // kept in sync with the first selected author so inserts never fail.
            'auth_id' => $validated['authors'][0],
            'cate_id' => $validated['cate_id'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
            'description' => $validated['description'] ?? null,
            'copyright_status' => $validated['copyright_status'] ?? 'copyrighted',
            'reading_url' => $validated['reading_url'] ?? null,
            'cover_image' => $coverPath,
            'is_archived' => $request->boolean('is_archived'),
        ]);

        $book->authors()->attach($validated['authors']);

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
            'category',
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

        $book->load('authors');

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
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'authors' => ['required', 'array', 'min:1'],
            'authors.*' => ['exists:authors,auth_id'],
            'cate_id' => ['nullable', 'exists:book_categories,cate_id'],
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
            // keep the legacy single-author column in sync with the pivot
            'auth_id' => $validated['authors'][0],
            'description' => $validated['description'] ?? null,
            'published_year' => $validated['published_year'] ?? null,
            'cover_image' => $coverPath,
            'cate_id' => $validated['cate_id'] ?? null,
            'copyright_status' => $validated['copyright_status'] ?? 'copyrighted',
            'reading_url' => $validated['reading_url'] ?? null,
            'is_archived' => $request->boolean('is_archived'),
        ]);

        $book->authors()->sync($validated['authors']);

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
}
