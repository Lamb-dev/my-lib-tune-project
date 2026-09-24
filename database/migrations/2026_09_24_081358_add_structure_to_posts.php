<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'author_name')) {
                $table->string('author_name')->nullable()->after('title');
            }
            if (! Schema::hasColumn('posts', 'cover_image')) {
                $table->string('cover_image')->nullable()->after('body');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'cover_image']);
        });
    }
};
