<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenLibraryService
{
    protected string $baseUrl = 'https://openlibrary.org';

    public function search(string $query, int $limit = 20): array
    {
        $query = trim($query);

        $response = Http::withoutRedirecting()
            ->withHeaders([
                'User-Agent' => 'LibTune/1.0',
                'Accept' => 'application/json',
            ])
            ->timeout(10)
            ->get(
                $this->baseUrl . '/search.json?q=' . urlencode($query) . '&limit=' . $limit
            );

        if ($response->failed()) {
            return [];
        }

        $data = $response->json();

        return $data['docs'] ?? [];
    }
}
