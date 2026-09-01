<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| NOTE
|--------------------------------------------------------------------------
| Laravel ships with its own 0001_01_01_000000_create_users_table.php
| migration (plus password_reset_tokens / sessions in the same file).
| Delete or replace that default migration with this one so you don't
| end up with two "users" table definitions. Keep the password_reset_tokens
| and sessions table definitions from the original file if you're using
| Breeze's password reset / session-based auth — just move them into
| their own migration or leave them in the default file and only remove
| the users table portion from it.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            // Not in the ERD, but required by Laravel/Breeze auth.
            $table->string('password');
            $table->rememberToken();
            $table->enum('role', ['user', 'admin'])->default('user');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
