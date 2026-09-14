<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id('book_id');
            $table->string('title',255);
            $table->text('description')->nullable();
            $table->integer('published_year')->nullable();

            $table->foreignId('auth_id')
                ->constrained(table: 'authors', column: 'auth_id')
                ->cascadeOnDelete();

            $table->foreignId('cate_id')
                ->nullable()
                ->constrained(table: 'book_categories', column: 'cate_id')
                ->nullOnDelete();

             $table->enum('copyright_status', [
             'public_domain',
             'copyrighted'
            ])->default('copyrighted');
            $table->string('reading_url',500)->nullable();
            $table->string('cover_image',255)->nullable();
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
