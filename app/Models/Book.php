<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\BookCategory;
class Book extends Model
{
    use HasFactory;

    protected $primaryKey = 'book_id';
    protected $table = 'books';
    protected $fillable = [
        'title',
        'description',
        'published_year',
        'cate_id',
        'copyright_status',
        'reading_url',
        'cover_image',
        'is_archived'
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
    return $this->belongsToMany(
        User::class,
        'saved_books',
        'book_id',
        'user_id'
        )->withTimestamps();
    }
    public function progress(): HasMany
    {
        return $this->hasMany(ProgressBook::class, 'book_id', 'book_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'book_id', 'book_id');
    }
    public function edit(Book $book)
    {
    $authors = Author::orderBy('name')->get();

    $categories = BookCategory::orderBy('cate_name')->get();

    return view('admin.books.edit', compact(
        'book',
        'authors',
        'categories'
    ));
    }
    public function isReadable(): bool
{
    return !empty($this->reading_url);
}
public function averageRating(): float
{
    return (float) $this->ratings()->avg('score');
}
}
