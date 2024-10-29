<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Dto\MovieDto;
use App\Dto\MovieListResponseDto;

interface MovieApiClientInterface
{
    public function getMovies(array $selectedGenres = []  ): MovieListResponseDto;
    public function searchMovies(string $query): \ArrayIterator|array;
    public function getGenres(): array;

    public function getMovieDetails(int $movieId): MovieDto;
}