<?php

namespace App\Service\Api;

use App\Contracts\MovieApiClientInterface;
use App\Dto\MovieDto;
use App\Dto\MovieListResponseDto;
use App\Exception\ApiException;
use App\Service\Serializer\MovieSerializer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class TmdbApiClient implements MovieApiClientInterface
{
    public const LANGUAGE_FR = 'fr-FR';

    public function __construct(
        #[Autowire(value: '%tmdb.api_token%')] private string   $apiToken,
        #[Autowire(value: '%tmdb.api_version%')] private string $apiVersion,
        private HttpClientInterface                             $tmdbClient,
        private TmdbRequestBuilder                              $requestBuilder,
        private MovieSerializer                                 $serializer,
    )
    {
    }

    public function getMovies(array $selectedGenres = []): MovieListResponseDto
    {
        try {
            $query = $this->requestBuilder->buildMovieQuery($selectedGenres);
            $response = $this->makeRequest("/discover/movie", $query);

            $dto = $this->serializer->deserializeMovieList($response['results'] ?? []);

            return new MovieListResponseDto(
                page: $response['page'] ?? 1,
                totalResults: $response['total_results'] ?? 0,
                totalPages: $response['total_pages'] ?? 0,
                results: $dto
            );
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la récupération des films: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    private function makeRequest(string $endpoint, array $query): array
    {
        try {

            if ($this->apiVersion) {
                $endpoint = "/{$this->apiVersion}" . $endpoint;
            }

            $response = $this->tmdbClient->request('GET', $endpoint, [
                'headers' => [
                    'Authorization' => "Bearer {$this->apiToken}",
                ],
                'query' => $query,
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new ApiException(
                    "Erreur API HTTP {$response->getStatusCode()}: {$response->getContent(false)}"
                );
            }

            $data = $response->toArray();
            $this->checkResponseForErrors($data);

            return $data;

        } catch (TransportExceptionInterface $e) {
            throw new ApiException("Erreur de transport HTTP: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    private function checkResponseForErrors(array $response): void
    {
        if (isset($response['success']) && $response['success'] === false) {
            throw new ApiException(
                $response['status_message'] ?? 'Une erreur inconnue est survenue',
                $response['status_code'] ?? 500
            );
        }

        if (isset($response['errors']) && !empty($response['errors'])) {
            throw new ApiException(
                implode(', ', $response['errors']),
                500
            );
        }

        if (isset($response['status_code']) && $response['status_code'] !== 1) {
            throw new ApiException(
                $response['status_message'] ?? 'Erreur de l\'API TMDB',
                $response['status_code'] ?? 500
            );
        }
    }

    public function searchMovies(string $query): array
    {
        try {
            $params = $this->requestBuilder->buildSearchQuery($query);
            $response = $this->makeRequest("/search/movie", $params);

            return $this->serializer->deserializeMovieList($response['results'] ?? []);
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la recherche de films: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getMovieDetails(int $movieId): MovieDto
    {
        try {
            $query = $this->requestBuilder->buildMovieDetailsQuery();
            $response = $this->makeRequest("/movie/{$movieId}", $query);

            return $this->serializer->deserializeMovie($response);
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la récupération des détails du film: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getGenres(): array
    {
        try {
            $query = $this->requestBuilder->buildGenreQuery();
            $response = $this->makeRequest("/genre/movie/list", $query);

            return $this->serializer->deserializeGenres($response['genres'] ?? []);
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la récupération des genres: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

}