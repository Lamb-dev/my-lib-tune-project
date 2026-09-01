<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('rating_id');

            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained(table: 'books', column: 'book_id')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('score'); // 1-5
            $table->text('review')->nullable();

            $table->timestamps();

            // One rating per user per book — re-rating should update this row, not add a new one.
            $table->unique(['user_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
