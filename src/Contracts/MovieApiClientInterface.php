<?php

declare(strict_types=1);

namespace App\Contracts;

interface MovieApiClientInterface
{
    public function getMovies(array $genres = []): array;
    public function searchMovies(string $query): \ArrayIterator|array;
    public function getGenres(): array;

    public function getMovieDetails(int $movieId): array;
}