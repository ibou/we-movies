<?php

declare(strict_types=1);

namespace App\Service\Serializer;

use App\Dto\GenreDto;
use App\Dto\MovieDto;
use App\Service\Mapper\GenreMapper;

final readonly class MovieSerializer
{
    /**
     * Désérialise une liste de films
     */
    public function deserializeMovieList(array $moviesData): array
    {
        return array_map(
            fn(array $movieData): \App\Dto\MovieDto => $this->deserializeMovie($movieData),
            $moviesData
        );
    }

    public function serializeMovieList(array $movies): array
    {
        return array_map(
            fn(MovieDto $movieDto): array => $this->serializeMovie($movieDto),
            $movies
        );
    }

    /**
     * Désérialise un film unique
     */
    public function deserializeMovie(array $movieData ): MovieDto
    {
        return MovieDto::fromArray($movieData);
    }

    public function serializeMovie(MovieDto $movieDto): array
    {
        return $movieDto->toArray();
    }

    /**
     * Désérialise une liste de genres
     */
    public function deserializeGenres(array $genresData): array
    {
        return array_map(
            fn(array $genreData): \App\Dto\GenreDto => GenreDto::fromArray($genreData),
            $genresData
        );
    }
}