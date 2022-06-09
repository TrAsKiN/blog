<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Repository\PostRepository;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HomeController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    #[Route('/', name: 'home')]
    public function home(PostRepository $postRepository): ResponseInterface
    {
        $posts = $postRepository->getPaginatedList(1, 3);
        return $this->render('home/home.html.twig', compact('posts'));
    }
}
