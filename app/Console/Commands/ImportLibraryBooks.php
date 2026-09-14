<?php
 
namespace App\Console\Commands;
 
use App\Services\OpenLibraryService;
use Illuminate\Console\Command;
 
class ImportLibraryBooks extends Command
{
    protected $signature = 'library:import-bulk {--per-topic=25 : How many books to import per subject}';
 
    protected $description = 'Bulk-import a real starter catalog from Open Library across several subjects';
 
    // A spread of subjects so the catalog isn't one-note. Feel free to
    // edit this list later to match categories you actually care about.
    protected array $topics = [
        'classic literature',
        'romance',
        'science fiction',
        'fantasy',
        'mystery',
        'adventure',
        'poetry',
        'history',
        'philosophy',
        'biography',
    ];
 
    public function handle(OpenLibraryService $openLibrary): int
    {
        $perTopic = (int) $this->option('per-topic');
        $totalImported = 0;
 
        $this->info("Importing up to {$perTopic} books each for " . count($this->topics) . ' subjects...');
        $bar = $this->output->createProgressBar(count($this->topics));
 
        foreach ($this->topics as $topic) {
            $results = $openLibrary->search($topic, $perTopic);
            $imported = $openLibrary->importResults($results);
            $totalImported += $imported->count();
 
            $bar->advance();
 
            // Be polite to Open Library's free public API rather than
            // hammering it with back-to-back requests.
            usleep(300_000);
        }
 
        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Imported/updated {$totalImported} books across " . count($this->topics) . ' subjects.');
 
        return self::SUCCESS;
    }
}