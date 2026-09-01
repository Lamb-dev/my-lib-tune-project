<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_books', function (Blueprint $table) {
            $table->id('saved_id');

            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained(table: 'books', column: 'book_id')
                ->cascadeOnDelete();

            $table->timestamps();

            // A user shouldn't be able to save the same book twice.
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_books');
    }
};
