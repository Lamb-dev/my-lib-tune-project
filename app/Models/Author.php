<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Author extends Model
{
    use HasFactory;

    protected $primaryKey = 'auth_id';

    protected $fillable = [
        'name',
        'biography',
        'birth_date',
        'nationality',
        'photo',

    ];

     public function books(): BelongsToMany
    {
        return $this->belongsToMany(
            Book::class,
            'book_authors',
            'auth_id',
            'book_id',
            'auth_id',
            'book_id'
        );
    }
}
