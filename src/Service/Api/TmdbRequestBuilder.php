<?php

declare(strict_types=1);

namespace App\Service\Api;

class TmdbRequestBuilder
{
    public function buildMovieQuery(array $genres = []): array
    {
        $query = [
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];

        if (!empty($genres)) {
            $query['with_genres'] = implode(',', $genres);
        }

        return $query;
    }

    public function buildSearchQuery(string $searchTerm): array
    {
        return [
            'query' => $searchTerm,
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];
    }

    public function buildGenreQuery(): array
    {
        return [
            'language' => TmdbApiClient::LANGUAGE_FR,
        ];
    }

    public function buildMovieDetailsQuery()
    {
        return [
            'language' => TmdbApiClient::LANGUAGE_FR,
        ];
    }

}