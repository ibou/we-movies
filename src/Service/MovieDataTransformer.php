<?php

declare(strict_types=1);

namespace App\Service;

use App\Contracts\MovieDataTransformerInterface;

class MovieDataTransformer implements MovieDataTransformerInterface
{
    public const KEY_GENRE_ALL = 'genre_ids';
    public const KEY_GENRE_DETAILS = 'genres';

    public function transformWithGenres(array $movies, array $genres, string $keyGenre = self::KEY_GENRE_ALL): array
    {
        $genreMap = $this->createGenreMap($genres);

        $transformedMovies = static::KEY_GENRE_ALL === $keyGenre ?
            $this->attachGenresToMovies(
                movies: $movies,
                genreMap: $genreMap,
                keyGenre: static::KEY_GENRE_ALL
            ) : $this->attachGenresDetaisToMovies(
                movies: $movies,
                genreMap: $genreMap,
                keyGenre: static::KEY_GENRE_DETAILS
            );

        return [
            'movies' => $transformedMovies,
            'genres' => $genreMap,
        ];
    }

    private function createGenreMap(array $genres): array
    {
        $genreMap = [];
        foreach ($genres as $genre) {
            $genreMap[$genre['id']] = $genre['name'];
        }
        return $genreMap;
    }

    private function attachGenresToMovies(array $movies, array $genreMap, string $keyGenre): array
    {
        foreach ($movies as $key => $movie) {
            $movies[$key]['genres'] = [];
            foreach ($movie[$keyGenre] as $genreId) {
                $movies[$key]['genres'][$genreId] = $genreMap[$genreId];
            }
        }
        return $movies;
    }

    private function attachGenresDetaisToMovies(array $movies, array $genreMap, string $keyGenre): array
    {
        foreach ($movies as $key => $movie) {
            $movies[$key]['genres'] = array_combine(
                array_column($movie[$keyGenre], 'id'),
                array_column($movie[$keyGenre], 'name')
            );
        }
        return $movies;
    }
}