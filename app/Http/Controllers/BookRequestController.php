<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class BookRequestController extends Controller
{
    /**
     * A reader suggesting a book the admin should consider adding.
     * Lands in the posts table with status 'pending' for an admin to
     * review — see Admin\BookRequestController for that side of it.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author_name' => 'required|string|max:255',
            'body' => 'nullable|string|max:2000',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('book-requests', 'public');
        }

        Post::create([
            'title' => $validated['title'],
            'author_name' => $validated['author_name'],
            'body' => $validated['body'] ?? '',
            'cover_image' => $coverPath,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Thanks — we\'ll take a look.']);
        }

        return back()->with('success', 'Thanks — we\'ll take a look at adding it.');
    }
}
