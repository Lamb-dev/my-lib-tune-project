<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('progress_book', function (Blueprint $table) {
            if (! Schema::hasColumn('progress_book', 'is_finished')) {
                $table->boolean('is_finished')->default(false)->after('progress');
            }
        });
    }

    public function down(): void
    {
        Schema::table('progress_book', function (Blueprint $table) {
            $table->dropColumn('is_finished');
        });
    }
};
