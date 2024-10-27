<?php

declare(strict_types=1);

namespace Tests\Unit\Service\Api;

use App\Service\Api\TmdbApiClient;
use App\Service\Api\TmdbRequestBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class TmdbApiClientTest extends TestCase
{
    private const API_TOKEN = 'test_api_token';
    private TmdbApiClient $client;
    private MockObject&HttpClientInterface $httpClient;
    private MockObject&TmdbRequestBuilder $requestBuilder;
    private MockObject&ResponseInterface $response;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->requestBuilder = $this->createMock(TmdbRequestBuilder::class);
        $this->response = $this->createMock(ResponseInterface::class);

        $this->client = new TmdbApiClient(
            self::API_TOKEN,
            $this->httpClient,
            $this->requestBuilder
        );
    }

    public function testGetMovies(): void
    {
        // Arrange
        $genres = [28, 12];
        $expectedQuery = ['some' => 'params'];
        $expectedResponse = ['results' => []];

        $this->requestBuilder
            ->expects($this->once())
            ->method('buildMovieQuery')
            ->with($genres)
            ->willReturn($expectedQuery);

        $this->mockHttpRequest(
            '/discover/movie',
            $expectedQuery,
            $expectedResponse
        );

        // Act
        $result = $this->client->getMovies($genres);

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    public function testSearchMovies(): void
    {
        // Arrange
        $searchTerm = 'Matrix';
        $expectedQuery = ['query' => $searchTerm];
        $expectedResponse = ['results' => []];

        $this->requestBuilder
            ->expects($this->once())
            ->method('buildSearchQuery')
            ->with($searchTerm)
            ->willReturn($expectedQuery);

        $this->mockHttpRequest(
            '/search/movie',
            $expectedQuery,
            $expectedResponse
        );

        // Act
        $result = $this->client->searchMovies($searchTerm);

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetGenres(): void
    {
        // Arrange
        $expectedQuery = ['language' => TmdbApiClient::LANGUAGE_FR];
        $expectedResponse = ['genres' => []];

        $this->requestBuilder
            ->expects($this->once())
            ->method('buildGenreQuery')
            ->willReturn($expectedQuery);

        $this->mockHttpRequest(
            '/genre/movie/list',
            $expectedQuery,
            $expectedResponse
        );

        // Act
        $result = $this->client->getGenres();

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetMovieDetails(): void
    {
        // Arrange
        $movieId = 123;
        $expectedQuery = ['language' => TmdbApiClient::LANGUAGE_FR];
        $expectedResponse = ['id' => $movieId, 'title' => 'Test Movie'];

        $this->requestBuilder
            ->expects($this->once())
            ->method('buildMovieDetailsQuery')
            ->willReturn($expectedQuery);

        $this->mockHttpRequest(
            "/movie/{$movieId}",
            $expectedQuery,
            $expectedResponse
        );

        // Act
        $result = $this->client->getMovieDetails($movieId);

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    private function mockHttpRequest(string $endpoint, array $query, array $responseData): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                'https://api.themoviedb.org/3' . $endpoint,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . self::API_TOKEN,
                        'accept' => 'application/json',
                    ],
                    'query' => $query,
                ]
            )
            ->willReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('toArray')
            ->willReturn($responseData);
    }

    public function testGetMoviesWithEmptyGenres(): void
    {
        // Arrange
        $expectedQuery = ['some' => 'params'];
        $expectedResponse = ['results' => []];

        $this->requestBuilder
            ->expects($this->once())
            ->method('buildMovieQuery')
            ->with([])
            ->willReturn($expectedQuery);

        $this->mockHttpRequest(
            '/discover/movie',
            $expectedQuery,
            $expectedResponse
        );

        // Act
        $result = $this->client->getMovies();

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }

    public function testSearchMoviesWithEmptyQuery(): void
    {
        // Arrange
        $expectedQuery = ['query' => ''];
        $expectedResponse = ['results' => []];

        $this->requestBuilder
            ->expects($this->once())
            ->method('buildSearchQuery')
            ->with('')
            ->willReturn($expectedQuery);

        $this->mockHttpRequest(
            '/search/movie',
            $expectedQuery,
            $expectedResponse
        );

        // Act
        $result = $this->client->searchMovies('');

        // Assert
        $this->assertEquals($expectedResponse, $result);
    }
}