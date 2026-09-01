<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $primaryKey = 'book_id';

    protected $fillable = [
        'title',
        'description',
        'published_year',
        'auth_id',
        'cate_id',
        'is_copyrighted',
        'file_path',
        'cover_image',
    ];

    protected function casts(): array
    {
        return [
            'is_copyrighted' => 'boolean',
            'published_year' => 'integer',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class, 'auth_id', 'auth_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'cate_id', 'cate_id');
    }

    public function savedBy(): HasMany
    {
        return $this->hasMany(SavedBook::class, 'book_id', 'book_id');
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ProgressBook::class, 'book_id', 'book_id');
    }

    public function accessGrants(): HasMany
    {
        return $this->hasMany(Access::class, 'book_id', 'book_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'book_id', 'book_id');
    }

    /** Enforces your "only non-copyrighted books can be read online" rule. */
    public function isReadable(): bool
    {
        return ! $this->is_copyrighted && ! empty($this->file_path);
    }

    public function averageRating(): float
    {
        return round((float) $this->ratings()->avg('score'), 1);
    }
}
