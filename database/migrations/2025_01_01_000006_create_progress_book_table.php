<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_book', function (Blueprint $table) {
            $table->id('progress_id');

            $table->foreignId('book_id')
                ->constrained(table: 'books', column: 'book_id')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            // e.g. an epubjs CFI string marking exactly where the reader left off
            $table->string('last_read')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_book');
    }
};
