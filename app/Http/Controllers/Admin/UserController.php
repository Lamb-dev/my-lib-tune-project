<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['ratings', 'savedBooks'])
            ->latest()
            ->paginate(15);

        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $newThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return view('admin.users.index', compact(
            'users',
            'totalUsers',
            'totalAdmins',
            'newThisMonth'
        ));
    }

    /**
     * Grant a reader admin-panel access.
     *
     * The admin panel is gated by a completely separate `admins` table
     * and guard, not by users.role — so promoting has to create a real
     * row there, not just flip a flag.
     *
     * Deliberately does NOT reuse the reader's existing password hash.
     * A reader account and an admin account are different trust
     * boundaries — a casual, possibly-weak or possibly-reused reader
     * password shouldn't double as the key to full site administration.
     * Instead this generates a fresh random password, shown exactly once
     * on the next page so it can be handed to the new admin directly;
     * it is never written to a log or stored anywhere in plain text.
     * They should change it on first login.
     */
    public function promote(User $user): RedirectResponse
    {
        if (Admin::where('admin_email', $user->email)->exists()) {
            return back()->with('success', "{$user->username} already has admin access.");
        }

        $temporaryPassword = Str::password(16);

        Admin::create([
            'admin_username' => $user->username,
            'admin_email' => $user->email,
            'admin_password' => Hash::make($temporaryPassword),
        ]);

        $user->update(['role' => 'admin']);

        return back()->with([
            'success' => "{$user->username} can now sign in to the admin panel.",
            'new_admin_email' => $user->email,
            'new_admin_password' => $temporaryPassword,
        ]);
    }

    /**
     * Revoke a reader's admin-panel access. The last remaining admin
     * account can't be revoked this way — that would lock everyone,
     * including whoever is doing the revoking, out of the panel entirely
     * with no way back in short of editing the database directly.
     */
    public function demote(User $user): RedirectResponse
    {
        if (Admin::count() <= 1) {
            return back()->with('error', "Can't revoke the last remaining admin account.");
        }

        Admin::where('admin_email', $user->email)->delete();
        $user->update(['role' => 'user']);

        return back()->with('success', "{$user->username}'s admin access has been revoked.");
    }
    public function toggleStatus(User $user)
    {
    $user->update(['is_active' => ! $user->is_active]);

    return back()->with('success', $user->username . ' is now ' . ($user->is_active ? 'active' : 'inactive') . '.');
    }
}
