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
            [
                'title' => 'Pride and Prejudice',
                'description' => 'A witty exploration of manners, upbringing, and marriage in Georgian England, following Elizabeth Bennet and the proud Mr. Darcy.',
                'published_year' => 1813,
                'author' => 'Jane Austen',
                'category' => 'Romance',
                'is_copyrighted' => false,
                'file_path' => null, // add a real epub path here once you have the file
            ],
            [
                'title' => "Alice's Adventures in Wonderland",
                'description' => 'A young girl falls through a rabbit hole into a world of nonsensical logic and strange characters.',
                'published_year' => 1865,
                'author' => 'Lewis Carroll',
                'category' => 'Fantasy',
                'is_copyrighted' => false,
                'file_path' => null,
            ],
            [
                'title' => 'Frankenstein',
                'description' => 'A scientist creates a sapient creature in an unorthodox experiment, with tragic consequences for creator and creation alike.',
                'published_year' => 1818,
                'author' => 'Mary Shelley',
                'category' => 'Gothic & Horror',
                'is_copyrighted' => false,
                'file_path' => null,
            ],
            [
                'title' => 'Moby-Dick',
                'description' => "Captain Ahab's obsessive pursuit of the white whale that took his leg, narrated by the sailor Ishmael.",
                'published_year' => 1851,
                'author' => 'Herman Melville',
                'category' => 'Adventure',
                'is_copyrighted' => false,
                'file_path' => null,
            ],
            [
                // Deliberately copyrighted: this proves your "catalog-only,
                // not readable" rule actually works end to end. Searchable
                // and browsable, but isReadable() returns false.
                'title' => "Harry Potter and the Sorcerer's Stone",
                'description' => 'A young boy discovers he is a wizard on his eleventh birthday. Still under copyright — listed here for catalog completeness only, not available to read online.',
                'published_year' => 1997,
                'author' => 'J.K. Rowling',
                'category' => 'Fantasy',
                'is_copyrighted' => true,
                'file_path' => null,
            ],
        ];
 
        foreach ($books as $book) {
            Book::updateOrCreate(
                ['title' => $book['title']],
                [
                    'description' => $book['description'],
                    'published_year' => $book['published_year'],
                    'auth_id' => $authors[$book['author']],
                    'cate_id' => $categories[$book['category']],
                    'is_copyrighted' => $book['is_copyrighted'],
                    'file_path' => $book['file_path'],
                ]
            );
        }
    }
}