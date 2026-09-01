<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->foreignId('following_user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            $table->foreignId('followed_user_id')
                ->constrained(table: 'users', column: 'user_id')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['following_user_id', 'followed_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
