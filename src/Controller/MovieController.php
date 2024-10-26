<?php

namespace App\Controller;

use App\Service\MovieService;
use App\Service\TmdbApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    public function __construct(
        private TmdbApiClient $movieService
    )
    {
    }

    #[Route('/', name: 'app_movie')]
    public function index(Request $request): Response
    {
        $query = $request->query->get('query');
        $selectedGenres = $request->query->all('genres');

        if ($query) {
            $movies = $this->movieService->searchMovies(query: $query);
        } else {
            $movies = $this->movieService->getMovies( genres: $selectedGenres );
        }
        $genres = $this->movieService->getGenres();
        $moviesWithGenres = $this->movieService->getMoviesWithGenres($movies['results'], $genres['genres']);

        return $this->render('movie/index.html.twig', [
            'movies' => $moviesWithGenres['movies'],
            'genres' => $moviesWithGenres['genres'],
            'page' => $movies['page'],
            'total_pages' => $movies['total_pages'],
            'selected_genres' => $selectedGenres,
            'total_results' => $movies['total_results'],

        ]);
    }

}
