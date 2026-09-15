<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

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
}
