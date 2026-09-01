<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    
    
    


    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function savedBooks(): HasMany
    {
        return $this->hasMany(SavedBook::class, 'user_id', 'user_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ProgressBook::class, 'user_id', 'user_id');
    }

    public function accessGrants(): HasMany
    {
        return $this->hasMany(Access::class, 'user_id', 'user_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'user_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'user_id', 'user_id');
    }

    /** Users this user follows. */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'following_user_id',
            'followed_user_id',
            'user_id',
            'user_id'
        );
    }

    /** Users following this user. */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'follows',
            'followed_user_id',
            'following_user_id',
            'user_id',
            'user_id'
        );
    }
}
