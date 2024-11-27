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
        private HttpClientInterface                             $httpClient,
        private TmdbRequestBuilder                              $tmdbRequestBuilder,
        private MovieSerializer                                 $movieSerializer,
    )
    {
    }

    public function getMovies(array $selectedGenres = []): MovieListResponseDto
    {
        try {
            $query = $this->tmdbRequestBuilder->buildMovieQuery($selectedGenres);
            $response = $this->makeRequest("/discover/movie", $query);

            $dto = $this->movieSerializer->deserializeMovieList($response['results'] ?? []);

            return new MovieListResponseDto(
                page: $response['page'] ?? 1,
                totalResults: $response['total_results'] ?? 0,
                totalPages: $response['total_pages'] ?? 0,
                results: $dto
            );
        } catch (\Exception $exception) {
            throw new ApiException("Erreur lors de la récupération des films: " . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    private function makeRequest(string $endpoint, array $query): array
    {
        try {

            if ($this->apiVersion !== '' && $this->apiVersion !== '0') {
                $endpoint = '/' . $this->apiVersion . $endpoint;
            }

            $response = $this->httpClient->request('GET', $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                ],
                'query' => $query,
            ]);

            if ($response->getStatusCode() !== Response::HTTP_OK) {
                throw new ApiException(
                    sprintf('Erreur API HTTP %s: %s', $response->getStatusCode(), $response->getContent(false))
                );
            }

            $data = $response->toArray();
            $this->checkResponseForErrors($data);

            return $data;

        } catch (TransportExceptionInterface $transportException) {
            throw new ApiException("Erreur de transport HTTP: " . $transportException->getMessage(), $transportException->getCode(), $transportException);
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
                $response['status_message'] ?? "Erreur de l'API TMDB",
                $response['status_code'] ?? 500
            );
        }
    }

    public function searchMovies(string $query): array
    {
        try {
            $params = $this->tmdbRequestBuilder->buildSearchQuery($query);
            $response = $this->makeRequest("/search/movie", $params);

            return $this->movieSerializer->deserializeMovieList($response['results'] ?? []);
        } catch (\Exception $exception) {
            throw new ApiException("Erreur lors de la recherche de films: " . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getMovieDetails(int $movieId): MovieDto
    {
        try {
            $query = $this->tmdbRequestBuilder->buildMovieDetailsQuery();
            $response = $this->makeRequest('/movie/' . $movieId, $query);

            return $this->movieSerializer->deserializeMovie($response);
        } catch (\Exception $exception) {
            throw new ApiException("Erreur lors de la récupération des détails du film: " . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getGenres(): array
    {
        try {
            $query = $this->tmdbRequestBuilder->buildGenreQuery();
            $response = $this->makeRequest("/genre/movie/list", $query);

            return $this->movieSerializer->deserializeGenres($response['genres'] ?? []);
        } catch (\Exception $exception) {
            throw new ApiException("Erreur lors de la récupération des genres: " . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

}