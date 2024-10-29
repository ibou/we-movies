<?php

declare(strict_types=1);

namespace App\Dto;

class MovieDto
{

    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $overview,
        public readonly ?string $posterPath,
        public readonly ?string $releaseDate,
        public readonly float $voteAverage,
        public array $genreIds,
        public array $genres = [],
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            overview: $data['overview'],
            posterPath: $data['poster_path'] ?? null,
            releaseDate: $data['release_date'] ?? null,
            voteAverage: $data['vote_average'],
            genreIds: $data['genre_ids'] ?? [],
            genres: $data['genres'] ?? [],
        );
    }
}
