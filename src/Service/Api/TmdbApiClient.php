<?php

declare(strict_types=1);

namespace App\Service\Api;

use App\Contracts\MovieApiClientInterface;
use App\Dto\MovieDto;
use App\Exception\ApiException;
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
        private TmdbRequestBuilder                              $requestBuilder
    )
    {
    }

    /**
     * Récupère une liste de films selon les genres spécifiés
     *
     * @param array $genres Liste des IDs de genres
     * @return array
     * @throws ApiException
     */
    public function getMovies(array $genres = []): array
    {
        try {
            $query = $this->requestBuilder->buildMovieQuery($genres);
            $movies = $this->makeRequest("/discover/movie", $query);

            return $movies;
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la récupération des films: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Effectue une requête HTTP vers l'API TMDB
     *
     * @param string $endpoint Point d'entrée de l'API
     * @param array $query Paramètres de la requête
     * @return array
     * @throws ApiException
     */
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

    /**
     * Vérifie si la réponse contient des erreurs
     *
     * @param array $response
     * @throws ApiException
     */
    private function checkResponseForErrors(array $response): void
    {
        if (isset($response['success']) && $response['success'] === false) {
            throw new ApiException(
                $response['status_message'] ?? 'Une erreur inconnue est survenue',
                $response['status_code'] ?? 500
            );
        }

        // Vérification supplémentaire pour les erreurs spécifiques de TMDB
        if (isset($response['errors']) && !empty($response['errors'])) {
            throw new ApiException(
                implode(', ', $response['errors']),
                500
            );
        }

        // Vérification du statut de la requête
        if (isset($response['status_code']) && $response['status_code'] !== 1) {
            throw new ApiException(
                $response['status_message'] ?? 'Erreur de l\'API TMDB',
                $response['status_code'] ?? 500
            );
        }
    }

    /**
     * Effectue une recherche de films par mot-clé
     *
     * @param string $query Terme de recherche
     * @return array
     * @throws ApiException
     */
    public function searchMovies(string $query): \ArrayIterator| array
    {
        try {
            $params = $this->requestBuilder->buildSearchQuery($query);
            $response = $this->makeRequest("/search/movie", $params);
            $moviesDto = array_map(
                fn(array $movieData) => MovieDto::fromArray($movieData),
                $response['results'] ?? []
            );
            $arrayTraversable = new \ArrayIterator($moviesDto);
            $arrayTraversable->offsetSet('page', $response['page'] ?? 1);
            $arrayTraversable->offsetSet('total_pages', $response['total_pages'] ?? 1);
            $arrayTraversable->offsetSet('total_results', $response['total_results'] ?? 1);
            return $arrayTraversable;
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la recherche de films: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Récupère la liste des genres de films
     *
     * @return array
     * @throws ApiException
     */
    public function getGenres(): array
    {
        try {
            $query = $this->requestBuilder->buildGenreQuery();
            return $this->makeRequest("/genre/movie/list", $query);
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la récupération des genres: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Récupère les détails d'un film spécifique
     *
     * @param int $movieId ID du film
     * @return array
     * @throws ApiException
     */
    public function getMovieDetails(int $movieId): array
    {
        try {
            $query = $this->requestBuilder->buildMovieDetailsQuery();
            return $this->makeRequest("/movie/{$movieId}", $query);
        } catch (\Exception $e) {
            throw new ApiException("Erreur lors de la récupération des détails du film: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
}