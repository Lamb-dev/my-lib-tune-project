<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->enum('copyright_status', ['public_domain', 'copyrighted'])
                ->default('copyrighted')
                ->after('cate_id');

            $table->string('reading_url')->nullable()->after('copyright_status');
            $table->boolean('is_archived')->default(false)->after('reading_url');
        });

        // Backfill copyright_status from the old boolean column.
        DB::table('books')->where('is_copyrighted', true)->update(['copyright_status' => 'copyrighted']);
        DB::table('books')->where('is_copyrighted', false)->update(['copyright_status' => 'public_domain']);

        // Backfill book_authors pivot rows from the old single auth_id
        // column, so existing books keep their author under the new
        // many-to-many system. INSERT IGNORE-equivalent via whereNotExists.
        $books = DB::table('books')->whereNotNull('auth_id')->select('book_id', 'auth_id')->get();
        foreach ($books as $book) {
            $exists = DB::table('book_authors')
                ->where('book_id', $book->book_id)
                ->where('auth_id', $book->auth_id)
                ->exists();

            if (! $exists) {
                DB::table('book_authors')->insert([
                    'book_id' => $book->book_id,
                    'auth_id' => $book->auth_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // auth_id is no longer the source of truth for authorship —
        // book_authors is. Drop its NOT NULL constraint so new code that
        // doesn't set it (per the new model) can still insert books.
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['auth_id']);
            $table->unsignedBigInteger('auth_id')->nullable()->change();
            $table->foreign('auth_id')->references('auth_id')->on('authors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['copyright_status', 'reading_url', 'is_archived']);
        });
    }
};
