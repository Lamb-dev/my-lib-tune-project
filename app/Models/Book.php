<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $primaryKey = 'book_id';
    protected $table = 'books';

    protected $fillable = [
        'title',
        'description',
        'published_year',
        'auth_id',
        'cate_id',
        'copyright_status',
        'reading_url',
        'cover_image',
        'is_archived',
        'file_path',
        'open_library_key',
    ];

    protected function casts(): array
    {
        return [
            'is_archived' => 'boolean',
            'published_year' => 'integer',
        ];
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(
            Author::class,
            'book_authors',
            'book_id',
            'auth_id',
            'book_id',
            'auth_id'
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'cate_id', 'cate_id');
    }

    public function savedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saved_books', 'book_id', 'user_id')->withTimestamps();
    }

    public function progress(): HasMany
    {
        return $this->hasMany(ProgressBook::class, 'book_id', 'book_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'book_id', 'book_id');
    }

    /**
     * A book is readable if it isn't copyrighted AND has somewhere to
     * actually read it — either our own hosted file, or an external link.
     */
    public function isReadable(): bool
    {
        return $this->copyright_status === 'public_domain'
            && (! empty($this->file_path) || ! empty($this->reading_url));
    }

    public function averageRating(): float
    {
        return round((float) $this->ratings()->avg('score'), 1);
    }

    /** Convenience accessor for displaying "by X, Y & Z" without repeating this everywhere. */
    public function authorNames(): string
    {
        return $this->authors->pluck('name')->join(', ', ' & ') ?: 'Unknown author';
    }
}
