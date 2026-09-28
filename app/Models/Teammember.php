<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $primaryKey = 'team_member_id';

    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo_path',
        'email',
        'linkedin_url',
        'twitter_url',
        'website_url',
        'sort_order',
    ];

    /**
     * Fallback shown in the avatar circle/card when no photo has been
     * uploaded yet — first letter of each word in the name, e.g. "PC".
     */
    public function initials(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->map(fn ($word) => strtoupper($word[0]))
            ->take(3)
            ->implode('');
    }
}
