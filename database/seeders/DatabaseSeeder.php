<?php

namespace Database\Seeders;

<<<<<<< Updated upstream
=======
use App\Models\User;
>>>>>>> Stashed changes
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< Updated upstream
        $this->call([
            AdminSeeder::class,
            BookCategorySeeder::class,
            AuthorSeeder::class,
=======
        User::factory()->create([
            'username' => 'testuser',
            'email' => 'test@example.com',
>>>>>>> Stashed changes
        ]);

        $this->call(LibrarySeeder::class);
    }
}