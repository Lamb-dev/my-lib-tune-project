<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id('auth_id');
            $table->string('name',100);
            $table->text('biography')->nullable();
            $table->timestamps();
            $table->date('birth_date')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('photo')->nullable();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
