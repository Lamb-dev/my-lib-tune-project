<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;

class BookRequestController extends Controller
{
    public function index()
    {
        $requests = Post::with('user')
            ->latest()
            ->paginate(15);

        $pendingCount = Post::where('status', 'pending')->count();

        return view('admin.book-requests.index', compact('requests', 'pendingCount'));
    }

    public function approve(Post $bookRequest): RedirectResponse
    {
        $bookRequest->update(['status' => 'approved']);

        return back()->with('success', 'Marked as approved.');
    }

    public function reject(Post $bookRequest): RedirectResponse
    {
        $bookRequest->update(['status' => 'rejected']);

        return back()->with('success', 'Marked as rejected.');
    }

    public function destroy(Post $bookRequest): RedirectResponse
    {
        // Also clean up the uploaded cover, if one was attached, rather
        // than leaving an orphaned file in storage.
        if ($bookRequest->cover_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($bookRequest->cover_image);
        }

        $bookRequest->delete();

        return back()->with('success', 'Suggestion deleted.');
    }
}
