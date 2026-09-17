<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Load saved books with authors and category
        $savedBooks = $user->savedBooks()->with(['authors', 'category'])->get();

        // Load progress books with book, and then book's authors and category
        $progressBooks = $user->progress()->with('book.authors', 'book.category')->get();

        // Get book IDs from progress books
        $progressBookIds = $progressBooks->pluck('book.book_id')->toArray();

        // Categorize saved books
        $wantToRead = $savedBooks->whereNotIn('book_id', $progressBookIds);
        $currentlyReading = $savedBooks->whereIn('book_id', $progressBookIds);

        return view('profile.edit', [
            'user' => $user,
            'savedBooks' => $savedBooks,
            'wantToRead' => $wantToRead,
            'currentlyReading' => $currentlyReading,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($user->profile_picture_path) {
                Storage::disk('public')->delete($user->profile_picture_path);
            }

            // Store the new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture_path = $path;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}