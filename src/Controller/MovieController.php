<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contracts\MovieApiClientInterface;
use App\Contracts\MovieDataTransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    public function __construct(
        public readonly MovieApiClientInterface       $movieService,
        public readonly MovieDataTransformerInterface $movieDataTransformer
    )
    {
    }

    #[Route('/', name: 'app_movie')]
    public function home(Request $request): Response
    {
        $query = $request->query->get('query');
        $selectedGenres = $request->query->all('genres');

        $movies = $this->fetchMovies(query: $query, selectedGenres: $selectedGenres);
        $genres = $this->movieService->getGenres();
        $moviesTransformedWithGenres = $this->movieDataTransformer->transformWithGenres(
            movies: $movies['results'],
            genres: $genres['genres'],
        );

        return $this->render('movie/index.html.twig', [
            'movies' => $moviesTransformedWithGenres['movies'],
            'genres' => $moviesTransformedWithGenres['genres'],
            'page' => $movies['page'],
            'total_pages' => $movies['total_pages'],
            'selected_genres' => $selectedGenres,
            'total_results' => $movies['total_results'],

        ]);
    }

    private function fetchMovies(?string $query, array $selectedGenres): array|\ArrayIterator
    {

        return $query
            ? $this->movieService->searchMovies($query)
            : $this->movieService->getMovies($selectedGenres);
    }

    #[Route('/movie/{movieId}/details', name: 'app_movie_details')]
    public function details(int $movieId): Response
    {

        $movie = $this->movieService->getMovieDetails($movieId);
        $genres = $this->movieService->getGenres();

        $movieTransformedWithGenres = $this->movieDataTransformer->transformWithGenres(
            movies: [$movie],
            genres: $genres['genres'],
            keyGenre: 'genres'
        );

        return $this->render('movie/details.html.twig', [
            'movie' => $movieTransformedWithGenres['movies'][0],
            'genres' => $movieTransformedWithGenres['genres'],
        ]);
    }
}
