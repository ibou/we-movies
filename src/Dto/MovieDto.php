<?php

declare(strict_types=1);

namespace App\Dto;

final readonly class MovieDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $originalTitle,
        public string $overview,
        public string $posterPath,
        public string $releaseDate,
        public float $voteAverage,
        public array $genres = [], // Format: [id => name]
        public array $genre_ids = [], // Format: [id, id, id]
        public bool $adult = false,
        public string $backdropPath = '',
        public float $popularity = 0.0,
        public bool $video = false,
        public int $voteCount = 0
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'],
            originalTitle: $data['original_title'],
            overview: $data['overview'] ?? '',
            posterPath: $data['poster_path'] ?? '',
            releaseDate: $data['release_date'] ?? '',
            voteAverage: $data['vote_average'] ?? 0.0,
            genres: $data['genres'] ?? [],
            genre_ids: $data['genre_ids'] ?? [],
            adult: $data['adult'] ?? false,
            backdropPath: $data['backdrop_path'] ?? '',
            popularity: $data['popularity'] ?? 0.0,
            video: $data['video'] ?? false,
            voteCount: $data['vote_count'] ?? 0
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'original_title' => $this->originalTitle,
            'overview' => $this->overview,
            'poster_path' => $this->posterPath,
            'release_date' => $this->releaseDate,
            'vote_average' => $this->voteAverage,
            'genres' => $this->genres,
            'genre_ids' => $this->genre_ids,
            'adult' => $this->adult,
            'backdrop_path' => $this->backdropPath,
            'popularity' => $this->popularity,
            'video' => $this->video,
            'vote_count' => $this->voteCount
        ];
    }
}