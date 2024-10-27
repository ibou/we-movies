<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Api;

use App\Service\Api\TmdbApiClient;
use App\Service\Api\TmdbRequestBuilder;
use PHPUnit\Framework\TestCase;

class TmdbRequestBuilderTest extends TestCase
{
    private TmdbRequestBuilder $requestBuilder;

    public function testBuildMovieQueryWithoutGenres(): void
    {
        // Act
        $query = $this->requestBuilder->buildMovieQuery();

        // Assert
        $expectedQuery = [
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];

        $this->assertEquals($expectedQuery, $query);
        $this->assertArrayNotHasKey('with_genres', $query);
    }

    public function testBuildMovieQueryWithGenres(): void
    {

        $genres = [28, 12];
        $query = $this->requestBuilder->buildMovieQuery($genres);
        $expectedQuery = [
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
            'with_genres' => '28,12',
        ];

        $this->assertEquals($expectedQuery, $query);
        $this->assertArrayHasKey('with_genres', $query);
        $this->assertEquals('28,12', $query['with_genres']);
    }

    public function testBuildSearchQuery(): void
    {
        $searchTerm = 'Matrix';

        $query = $this->requestBuilder->buildSearchQuery($searchTerm);

        $expectedQuery = [
            'query' => 'Matrix',
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];

        $this->assertEquals($expectedQuery, $query);
        $this->assertArrayHasKey('query', $query);
        $this->assertEquals($searchTerm, $query['query']);
    }

    public function testBuildSearchQueryWithEmptyString(): void
    {
        // Act
        $query = $this->requestBuilder->buildSearchQuery('');

        // Assert
        $expectedQuery = [
            'query' => '',
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];

        $this->assertEquals($expectedQuery, $query);
        $this->assertArrayHasKey('query', $query);
        $this->assertEmpty($query['query']);
    }

    public function testBuildGenreQuery(): void
    {
        // Act
        $query = $this->requestBuilder->buildGenreQuery();

        // Assert
        $expectedQuery = [
            'language' => TmdbApiClient::LANGUAGE_FR,
        ];

        $this->assertEquals($expectedQuery, $query);
    }

    public function testBuildMovieDetailsQuery(): void
    {
        // Act
        $query = $this->requestBuilder->buildMovieDetailsQuery();

        // Assert
        $expectedQuery = [
            'language' => TmdbApiClient::LANGUAGE_FR,
        ];

        $this->assertEquals($expectedQuery, $query);
    }

    public function testBuildMovieQueryWithEmptyGenresArray(): void
    {
        // Act
        $query = $this->requestBuilder->buildMovieQuery([]);

        // Assert
        $expectedQuery = [
            'language' => TmdbApiClient::LANGUAGE_FR,
            'page' => 1,
            'sort_by' => 'popularity.desc',
            'include_adult' => false,
            'include_video' => false,
        ];

        $this->assertEquals($expectedQuery, $query);
        $this->assertArrayNotHasKey('with_genres', $query);
    }

    protected function setUp(): void
    {
        $this->requestBuilder = new TmdbRequestBuilder();
    }
}