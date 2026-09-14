<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Services\OpenLibraryService;
use Illuminate\Console\Command;

class BackfillBookDetails extends Command
{
    protected $signature = 'library:backfill-details {--limit=500 : Max books to process in one run}';

    protected $description = 'Backfill category and description for previously-imported books that are missing them';

    public function handle(OpenLibraryService $openLibrary): int
    {
        $books = Book::whereNotNull('open_library_key')
            ->where(function ($q) {
                $q->whereNull('cate_id')->orWhereNull('description');
            })
            ->limit((int) $this->option('limit'))
            ->get();

        if ($books->isEmpty()) {
            $this->info('Nothing to backfill — every imported book already has a category and description.');
            return self::SUCCESS;
        }

        $this->info("Backfilling {$books->count()} books...");
        $bar = $this->output->createProgressBar($books->count());

        foreach ($books as $book) {
            $details = $openLibrary->fetchWorkDetails($book->open_library_key);

            $updates = [];

            if (! $book->description && $details['description']) {
                $updates['description'] = $details['description'];
            }

            if (! $book->cate_id) {
                $updates['cate_id'] = $openLibrary->categoryFromSubjects($details['subjects']);
            }

            if ($updates) {
                $book->update($updates);
            }

            $bar->advance();
            usleep(250_000); // stay polite to Open Library's free API
        }

        $bar->finish();
        $this->newLine(2);
        $this->info('Backfill complete.');

        $remaining = Book::whereNotNull('open_library_key')
            ->where(function ($q) {
                $q->whereNull('cate_id')->orWhereNull('description');
            })->count();

        if ($remaining > 0) {
            $this->comment("{$remaining} books still incomplete — run this command again to keep going (it processes {$this->option('limit')} at a time).");
        }

        return self::SUCCESS;
    }
}