<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Books move from a single cate_id to many-to-many categories, the
     * same way authorship moved to book_authors. cate_id on books is kept
     * (nullable) as the legacy "primary" category so existing single-
     * category code paths keep working, but book_category_pivot is now
     * the source of truth.
     */
    public function up(): void
    {
        Schema::create('book_category_pivot', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('book_id')
                ->constrained(table: 'books', column: 'book_id')
                ->cascadeOnDelete();

            $table->foreignId('cate_id')
                ->constrained(table: 'book_categories', column: 'cate_id')
                ->cascadeOnDelete();

            $table->unique(['book_id', 'cate_id']);
        });

        // Backfill from the existing single cate_id column so books keep
        // their category under the new many-to-many system.
        $books = DB::table('books')->whereNotNull('cate_id')->select('book_id', 'cate_id')->get();

        foreach ($books as $book) {
            DB::table('book_category_pivot')->insert([
                'book_id' => $book->book_id,
                'cate_id' => $book->cate_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_category_pivot');
    }
};
