<?php

declare(strict_types=1);

namespace App\Service\Mapper;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class GenreMapper
{
    private const CACHE_KEY = 'tmdb_genres_map';
    private const CACHE_TTL = 86400; // 24 heures

    private array $genres = [];

    public function __construct(
        //private TmdbApiClient $tmdbClient,
        private CacheInterface $cache
    ) {
    }

    /**
     * @param array $genres
     */
    public function setGenres(array $genres): void
    {
        $this->genres = $genres;
    }

    /**
     * Récupère la map des genres depuis l'API ou le cache
     */
    public function getGenreMap(): array
    {
            return $this->genres;
    }

    /**
     * Convertit un ID de genre en nom de genre
     */
    public function getGenreName(int $genreId): ?string
    {
        $genres = $this->getGenreMap();
        return $genres[$genreId] ?? null;
    }

    /**
     * Convertit une liste d'IDs de genres en tableau associatif [id => nom]
     */
    public function mapGenreIdsToNames(array $genreIds): array
    {
        $genres = $this->getGenreMap();
        return array_intersect_key(
            $genres,
            array_flip($genreIds)
        );
    }

    /**
     * Récupère tous les genres disponibles
     */
    public function getAllGenres(): array
    {
        return $this->getGenreMap();
    }

    /**
     * Force le rafraîchissement du cache des genres
     */
    public function refreshGenres(): array
    {
        $this->cache->delete(self::CACHE_KEY);
        return $this->getGenreMap();
    }

}