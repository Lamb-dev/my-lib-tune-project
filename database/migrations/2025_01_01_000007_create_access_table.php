<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access', function (Blueprint $table) {
            $table->id('access_id');

            $table->foreignId('user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained(table: 'books', column: 'book_id')
                ->cascadeOnDelete();

            $table->string('access_type')->default('read'); // e.g. read, download
            $table->timestamp('granted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access');
    }
};
