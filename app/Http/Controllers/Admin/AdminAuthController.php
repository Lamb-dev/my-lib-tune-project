<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'admin_email' => 'required|email',
            'admin_password' => 'required',
        ]);

        // Map your form input field 'admin_password' to the 'password' key expected by Auth::attempt()
        $authCredentials = [
            'admin_email' => $credentials['admin_email'],
            'password' => $credentials['admin_password'],
        ];

        if (Auth::guard('admin')->attempt($authCredentials)) {
            $request->session()->regenerate();

            return redirect()
            ->intended(route('admin.dashboard'))
            ->with('just_logged_in', true);
        }

        return back()->withErrors([
            'admin_email' => 'The admin email or password is incorrect.',
        ])->onlyInput('admin_email');
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
