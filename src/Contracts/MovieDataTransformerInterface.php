<?php

declare(strict_types=1);

namespace App\Contracts;

interface MovieDataTransformerInterface
{
    public function transformWithGenres(array $movies, array $genres, string $keyGenre): array;
}