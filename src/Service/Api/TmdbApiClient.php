<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Contracts\MovieApiClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbApiClient implements MovieApiClientInterface
{

    private const BASE_URL = 'https://api.themoviedb.org/3';
    public const LANGUAGE_FR = 'fr-FR';
    public const LANGUAGE_EN = 'en-US';

    public function __construct(
        #[Autowire(value: '%tmdb.api_token%')] private readonly string $apiToken,
        private readonly HttpClientInterface                           $httpClient,
        private readonly TmdbRequestBuilder                            $requestBuilder
    )
    {
    }

    public function getMovies(array $genres = []): array
    {
        $query = $this->requestBuilder->buildMovieQuery($genres);
        return $this->makeRequest("/discover/movie", $query);
    }

    private function makeRequest(string $endpoint, array $query): array
    {
        $response = $this->httpClient->request('GET', self::BASE_URL . $endpoint, [
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'accept' => 'application/json',
            ],
            'query' => $query,
        ]);

        return $response->toArray();
    }

    public function searchMovies(string $query): array
    {
        $params = $this->requestBuilder->buildSearchQuery($query);
        return $this->makeRequest("/search/movie", $params);
    }

    public function getGenres(): array
    {
        $query = $this->requestBuilder->buildGenreQuery();
        return $this->makeRequest("/genre/movie/list", $query);
    }

    public function getMovieDetails(int $movieId)
    {
        $query = $this->requestBuilder->buildMovieDetailsQuery();
        return $this->makeRequest("/movie/{$movieId}", $query);
    }
}