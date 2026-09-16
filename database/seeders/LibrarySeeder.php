<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\BookCategory;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Classic Literature',
            'Adventure',
            'Romance',
            'Gothic & Horror',
            'Fantasy',
        ])->mapWithKeys(function ($name) {
            $category = BookCategory::firstOrCreate(['cate_name' => $name]);
            return [$name => $category->cate_id];
        });

        $authors = collect([
            'Jane Austen' => 'English novelist known for her wit and social commentary on the landed gentry of the late 18th century.',
            'Lewis Carroll' => 'English author and mathematician, best remembered for his fantastical children\'s stories.',
            'Mary Shelley' => 'English novelist who wrote one of the earliest and most influential works of science fiction.',
            'Herman Melville' => 'American novelist and poet of the American Renaissance period.',
            'J.K. Rowling' => 'British author best known for a seven-book fantasy series that remains under copyright.',
        ])->map(function ($bio, $name) {
            return Author::firstOrCreate(['name' => $name], ['biography' => $bio])->auth_id;
        });

        $books = [
            ['title' => 'Pride and Prejudice', 'description' => 'A witty exploration of manners, upbringing, and marriage in Georgian England, following Elizabeth Bennet and the proud Mr. Darcy.', 'published_year' => 1813, 'author' => 'Jane Austen', 'category' => 'Romance', 'copyright_status' => 'public_domain'],
            ['title' => "Alice's Adventures in Wonderland", 'description' => 'A young girl falls through a rabbit hole into a world of nonsensical logic and strange characters.', 'published_year' => 1865, 'author' => 'Lewis Carroll', 'category' => 'Fantasy', 'copyright_status' => 'public_domain'],
            ['title' => 'Frankenstein', 'description' => 'A scientist creates a sapient creature in an unorthodox experiment, with tragic consequences for creator and creation alike.', 'published_year' => 1818, 'author' => 'Mary Shelley', 'category' => 'Gothic & Horror', 'copyright_status' => 'public_domain'],
            ['title' => 'Moby-Dick', 'description' => "Captain Ahab's obsessive pursuit of the white whale that took his leg, narrated by the sailor Ishmael.", 'published_year' => 1851, 'author' => 'Herman Melville', 'category' => 'Adventure', 'copyright_status' => 'public_domain'],
            ['title' => "Harry Potter and the Sorcerer's Stone", 'description' => 'A young boy discovers he is a wizard on his eleventh birthday. Still under copyright — listed for catalog completeness only, not available to read online.', 'published_year' => 1997, 'author' => 'J.K. Rowling', 'category' => 'Fantasy', 'copyright_status' => 'copyrighted'],
        ];

        foreach ($books as $bookData) {
            $book = Book::updateOrCreate(
                ['title' => $bookData['title']],
                [
                    'description' => $bookData['description'],
                    'published_year' => $bookData['published_year'],
                    'cate_id' => $categories[$bookData['category']],
                    'copyright_status' => $bookData['copyright_status'],
                ]
            );

            $authorId = $authors[$bookData['author']];
            if (! $book->authors()->where('authors.auth_id', $authorId)->exists()) {
                $book->authors()->attach($authorId);
            }

             $categoryId = $categories[$bookData['category']];
            if (! $book->categories()->where('book_categories.cate_id',
             $categoryId)->exists()) {
                $book->categories()->attach($categoryId);
            }
        }
    }
}
