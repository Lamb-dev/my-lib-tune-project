<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Follow extends Model
{
    protected $table = 'follows';

    // No single auto-incrementing primary key — it's a composite key
    // (following_user_id, followed_user_id), so disable Eloquent's
    // default id handling.
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'following_user_id',
        'followed_user_id',
    ];

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_user_id', 'user_id');
    }

    public function followed(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_user_id', 'user_id');
    }
}
