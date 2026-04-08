<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TmdbService
{
    private ?string $apiKey;
    private ?string $baseUrl;
    private ?string $imageUrl;

    public function __construct()
    {
        $this->apiKey   = config('services.tmdb.key');
        $this->baseUrl  = config('services.tmdb.base_url');
        $this->imageUrl = config('services.tmdb.image_url');
    }

    // Rechercher des films par titre
    public function searchMovies(string $query): array
    {
        $response = Http::get("{$this->baseUrl}/search/movie", [
            'api_key'  => $this->apiKey,
            'query'    => $query,
            'language' => 'fr-FR',
            'page'     => 1,
        ]);

        if ($response->failed()) {
            return [];
        }

        return collect($response->json('results'))
            ->map(function ($movie) {
                return [
                    'tmdb_id'      => $movie['id'],
                    'title'        => $movie['title'],
                    'release_year' => isset($movie['release_date'])
                                        ? substr($movie['release_date'], 0, 4)
                                        : null,
                    'poster_path'  => $movie['poster_path'] ?? null,
                    'overview'     => $movie['overview'] ?? '',
                    'genre_ids'    => $movie['genre_ids'] ?? [],
                ];
            })
            ->toArray();
    }

    // Récupérer les détails d'un film par son ID TMDB
    public function getMovie(int $tmdbId): ?array
    {
        $response = Http::get("{$this->baseUrl}/movie/{$tmdbId}", [
            'api_key'            => $this->apiKey,
            'language'           => 'fr-FR',
            'append_to_response' => 'credits',
        ]);

        if ($response->failed()) return null;

        $movie = $response->json();

        $director = collect($movie['credits']['crew'] ?? [])
            ->firstWhere('job', 'Director');

        $cast = collect($movie['credits']['cast'] ?? [])
            ->take(5)
            ->map(fn($actor) => [
                'name'      => $actor['name'],
                'character' => $actor['character'],
                'profile'   => $actor['profile_path'] ?? null,
            ])
            ->toArray();

        $genres = collect($movie['genres'] ?? [])
            ->pluck('name')
            ->toArray();

        return [
            'tmdb_id'       => $movie['id'],
            'title'         => $movie['title'],
            'release_year'  => isset($movie['release_date'])
                                ? substr($movie['release_date'], 0, 4)
                                : null,
            'release_date'  => $movie['release_date'] ?? null,
            'poster_path'   => $movie['poster_path'] ?? null,
            'backdrop_path' => $movie['backdrop_path'] ?? null,
            'overview'      => $movie['overview'] ?? '',
            'runtime'       => $movie['runtime'] ?? null,
            'vote_average'  => round($movie['vote_average'] ?? 0, 1),
            'genre'         => $genres[0] ?? null,
            'genres'        => $genres,
            'director'      => $director['name'] ?? null,
            'cast'          => $cast,
        ];
    }

    // Construire l'URL complète d'un poster
    public function getPosterUrl(?string $posterPath): ?string
    {
        if (!$posterPath) return null;
        return $this->imageUrl . $posterPath;
    }
}
