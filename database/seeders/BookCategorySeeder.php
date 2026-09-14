<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BookCategory;

class BookCategorySeeder extends Seeder
{
    public function run(): void
    {
        BookCategory::create([
            'cate_name' => 'Fiction',
        ]);

        BookCategory::create([
            'cate_name' => 'Science',
        ]);

        BookCategory::create([
            'cate_name' => 'Technology',
        ]);

        BookCategory::create([
            'cate_name' => 'History',
        ]);

        BookCategory::create([
            'cate_name' => 'Fantasy',
        ]);
    }
}