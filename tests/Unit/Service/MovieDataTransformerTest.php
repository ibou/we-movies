<?php

declare(strict_types=1);

namespace Tests\Unit\Service;

use App\Service\MovieDataTransformer;
use PHPUnit\Framework\TestCase;

class MovieDataTransformerTest extends TestCase
{
    private MovieDataTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new MovieDataTransformer();
    }

    public function testTransformWithGenresUsingGenreIds(): void
    {
        // Arrange
        $movies = [
            [
                'id' => 1,
                'title' => 'Test Movie',
                'genre_ids' => [28, 12]
            ]
        ];

        $genres = [
            ['id' => 28, 'name' => 'Action'],
            ['id' => 12, 'name' => 'Adventure']
        ];

        // Act
        $result = $this->transformer->transformWithGenres($movies, $genres);

        // Assert
        $this->assertArrayHasKey('movies', $result);
        $this->assertArrayHasKey('genres', $result);

        $expectedGenres = [
            28 => 'Action',
            12 => 'Adventure'
        ];

        $this->assertEquals($expectedGenres, $result['genres']);
        $this->assertArrayHasKey('genres', $result['movies'][0]);
        $this->assertEquals([
            28 => 'Action',
            12 => 'Adventure'
        ], $result['movies'][0]['genres']);
    }

    public function testTransformWithGenresUsingGenreDetails(): void
    {
        // Arrange
        $movies = [
            [
                'id' => 1,
                'title' => 'Test Movie',
                'genres' => [
                    ['id' => 28, 'name' => 'Action'],
                    ['id' => 12, 'name' => 'Adventure']
                ]
            ]
        ];

        $genres = [
            ['id' => 28, 'name' => 'Action'],
            ['id' => 12, 'name' => 'Adventure']
        ];

        // Act
        $result = $this->transformer->transformWithGenres(
            $movies,
            $genres,
            MovieDataTransformer::KEY_GENRE_DETAILS
        );

        // Assert
        $this->assertArrayHasKey('movies', $result);
        $this->assertArrayHasKey('genres', $result);

        $expectedGenres = [
            28 => 'Action',
            12 => 'Adventure'
        ];

        $this->assertEquals($expectedGenres, $result['genres']);
        $this->assertArrayHasKey('genres', $result['movies'][0]);
        $this->assertEquals($expectedGenres, $result['movies'][0]['genres']);
    }

    public function testTransformWithEmptyGenres(): void
    {
        // Arrange
        $movies = [
            [
                'id' => 1,
                'title' => 'Test Movie',
                'genre_ids' => []
            ]
        ];

        $genres = [];

        // Act
        $result = $this->transformer->transformWithGenres($movies, $genres);

        // Assert
        $this->assertArrayHasKey('movies', $result);
        $this->assertArrayHasKey('genres', $result);
        $this->assertEmpty($result['genres']);
        $this->assertEmpty($result['movies'][0]['genres']);
    }

    public function testTransformWithMultipleMovies(): void
    {
        // Arrange
        $movies = [
            [
                'id' => 1,
                'title' => 'Test Movie 1',
                'genre_ids' => [28]
            ],
            [
                'id' => 2,
                'title' => 'Test Movie 2',
                'genre_ids' => [12]
            ]
        ];

        $genres = [
            ['id' => 28, 'name' => 'Action'],
            ['id' => 12, 'name' => 'Adventure']
        ];

        // Act
        $result = $this->transformer->transformWithGenres($movies, $genres);

        // Assert
        $this->assertCount(2, $result['movies']);
        $this->assertEquals([28 => 'Action'], $result['movies'][0]['genres']);
        $this->assertEquals([12 => 'Adventure'], $result['movies'][1]['genres']);
    }
}