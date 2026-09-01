<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookCategory extends Model
{
    use HasFactory;

    protected $table = 'book_categories';

    protected $primaryKey = 'cate_id';

    protected $fillable = [
        'cate_name',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'cate_id', 'cate_id');
    }
}
