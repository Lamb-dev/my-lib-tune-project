<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BookAuth extends Model
{
    use HasFactory;

    protected $table = 'book_authors';

    protected $fillable = [
        'book_id',
        'auth_id',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(
            Author::class,
            'auth_id',
            'auth_id'
        );
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(
            Book::class,
            'book_id',
            'book_id'
        );
    }
    public function up()
{
    Schema::table('books', function (Blueprint $table) {
        // If auth_id was a foreign key, you must drop the constraint first.
        // If it wasn't a foreign key, you can remove the dropForeign line.
        $table->dropForeign(['auth_id']);
        $table->dropColumn('auth_id');
    });
}

public function down()
{
    Schema::table('books', function (Blueprint $table) {
        $table->foreignId('auth_id')->nullable()->constrained('authors');
    });
}
}
