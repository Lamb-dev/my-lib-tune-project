<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamMemberController extends Controller
{
    public function index()
    {
        $teamMembers = TeamMember::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.team.index', compact('teamMembers'));
    }

    public function create()
    {
        return view('admin.team.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('team', 'public');
        }

        TeamMember::create($validated);

        return redirect()
            ->route('admin.team.index')
            ->with('success', 'Team member added successfully.');
    }

    public function edit(TeamMember $team)
    {
        $teamMember = $team;

        return view('admin.team.edit', compact('teamMember'));
    }

    public function update(Request $request, TeamMember $team)
    {
        $validated = $this->validated($request);

        if ($request->hasFile('photo')) {
            if ($team->photo_path) {
                Storage::disk('public')->delete($team->photo_path);
            }

            $validated['photo_path'] = $request->file('photo')->store('team', 'public');
        } elseif ($request->boolean('remove_photo') && $team->photo_path) {
            Storage::disk('public')->delete($team->photo_path);
            $validated['photo_path'] = null;
        }

        $team->update($validated);

        return redirect()
            ->route('admin.team.index')
            ->with('success', 'Team member updated successfully.');
    }

    public function destroy(TeamMember $team)
    {
        if ($team->photo_path) {
            Storage::disk('public')->delete($team->photo_path);
        }

        $team->delete();

        return redirect()
            ->route('admin.team.index')
            ->with('success', 'Team member removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'email' => 'nullable|email|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);
    }
}
