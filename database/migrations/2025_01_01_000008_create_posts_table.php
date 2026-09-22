<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Book suggestions started as just a title + a freeform body. That's
     * too loose to review at a glance and too easy to fill with junk —
     * splitting author into its own field, and allowing an optional
     * cover image, makes a suggestion look and behave like a real book
     * record rather than an open text box.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('author_name')->nullable()->after('title');
            $table->string('cover_image')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['author_name', 'cover_image']);
        });
    }
};
