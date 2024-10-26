<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbApiClient
{

    private const BASE_URL = 'https://api.themoviedb.org/3';
    public const LANGUAGE_FR = 'fr-FR';
    public const LANGUAGE_EN = 'en-US';

    public function __construct(
        #[Autowire(value: '%tmdb.api_token%')]
        private readonly string $apiToken,
        private readonly HttpClientInterface $httpClient,
    )
    {
    }


    public function getMovies(array $genres = []): array
    {
        // if not empty genres add key genres to query
        $query = [
            'language' => static::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];
        if (!empty($genres)) {
            $query['with_genres'] = implode(',', $genres);
        }

        $response = $this->httpClient->request('GET', self::BASE_URL . "/discover/movie", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'accept' => 'application/json',
            ],
            'query' => $query
        ]);
        return $response->toArray();
    }

    public function searchMovies(string $query): array
    {


        $response = $this->httpClient->request('GET', self::BASE_URL . "/search/movie?query={$query}", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => static::LANGUAGE_FR,
                'page' => 1,
                'sort_by' => 'popularity.desc',
                'include_adult' => false,
                'include_video' => false,

            ]
        ]);

        return $response->toArray();
    }
    //get genre of movie
    public function getGenres(): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . "/genre/movie/list", [
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => static::LANGUAGE_FR,
            ]
        ]);

        return $response->toArray();
    }

    //combine results of getMovies and getGenres
    public function getMoviesWithGenres(array $movies, array $genres): array
    {
        $newGenres = [];
        foreach ($genres as $genre) {
            $newGenres[$genre['id']] = $genre['name'];
        }

        foreach ($movies as $key => $movie) {
            $movies[$key]['genres'] = [];
            foreach ($movie['genre_ids'] as $genreId) {
                $movies[$key]['genres'][$genreId] = $newGenres[$genreId];
            }
        }

        return [
            'movies' => $movies,
            'genres' => $newGenres
        ];
    }

}