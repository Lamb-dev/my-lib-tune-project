<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    use HasFactory;

    protected $primaryKey = 'auth_id';

    protected $fillable = [
        'name',
        'biography',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'auth_id', 'auth_id');
    }
}
