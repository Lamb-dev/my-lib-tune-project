<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| This is your "request to add a book" portal — a user posts a request
| (title/body = book they want added), an admin reviews it via status.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');

            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
