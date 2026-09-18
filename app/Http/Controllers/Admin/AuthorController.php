<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::withCount('books')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    $exists = Author::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($value))])
                        ->exists();

                    if ($exists) {
                        $fail('An author with this name already exists. Check the list before adding a new one.');
                    }
                },
            ],
            'biography' => 'nullable|string',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'nationality' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Normalize the stored value too — validation catches "Jane Austen"
        // vs "jane austen ", but without trimming here the sloppy version
        // could still be the one saved to the database.
        $validated['name'] = trim($validated['name']);

          if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')
                ->store('authors', 'public');
        }

        Author::create($validated);

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author added successfully.');
    }

    public function edit(Author $author)
    {
        return view('admin.authors.edit', compact('author'));
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) use ($author) {
                    $exists = Author::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($value))])
                        ->where('auth_id', '!=', $author->auth_id)
                        ->exists();

                    if ($exists) {
                        $fail('Another author with this name already exists.');
                    }
                },
            ],
            'biography' => 'nullable|string',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'nationality' => 'nullable|string|max:100',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['name'] = trim($validated['name']);

           if ($request->hasFile('photo')) {

            if ($author->photo) {
                Storage::disk('public')->delete($author->photo);
            }

            $validated['photo'] = $request->file('photo')
                ->store('authors', 'public');
        }

        $author->update($validated);

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author updated successfully.');
    }

    public function destroy(Author $author)
    {
        if ($author->photo) {
            Storage::disk('public')->delete($author->photo);
        }

        $author->delete();

        return redirect()
            ->route('admin.authors.index')
            ->with('success', 'Author deleted successfully.');
    }
}
