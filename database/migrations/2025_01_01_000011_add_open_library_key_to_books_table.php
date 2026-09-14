<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Lets us recognize a book we've already imported from Open
            // Library, so repeat searches update it instead of duplicating it.
            $table->string('open_library_key')->nullable()->unique()->after('cover_image');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('open_library_key');
        });
    }
};
