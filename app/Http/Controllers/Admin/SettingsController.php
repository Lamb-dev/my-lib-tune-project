<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Show the site settings form (currently just the logo).
     */
    public function edit()
    {
        $logoPath = $this->currentLogoPath();

        return view('admin.settings.edit', compact('logoPath'));
    }

    /**
     * Replace the site logo shown in the admin sidebar.
     */
    public function updateLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,webp,svg|max:1024',
        ]);

        // Remove any previously uploaded logo (whatever its extension was).
        if (Storage::disk('public')->exists('branding')) {
            foreach (Storage::disk('public')->files('branding') as $file) {
                if (str_starts_with(basename($file), 'logo.')) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $extension = $request->file('logo')->getClientOriginalExtension();
        $request->file('logo')->storeAs('branding', 'logo.' . $extension, 'public');

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Logo updated successfully.');
    }

    /**
     * Remove the custom logo and fall back to the default LibTune wordmark.
     */
    public function destroyLogo()
    {
        if (Storage::disk('public')->exists('branding')) {
            foreach (Storage::disk('public')->files('branding') as $file) {
                if (str_starts_with(basename($file), 'logo.')) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Logo removed. Showing the default LibTune logo.');
    }

    private function currentLogoPath(): ?string
    {
        if (!Storage::disk('public')->exists('branding')) {
            return null;
        }

        return collect(Storage::disk('public')->files('branding'))
            ->first(fn ($file) => str_starts_with(basename($file), 'logo.'));
    }
}
