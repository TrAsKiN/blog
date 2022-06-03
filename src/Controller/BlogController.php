<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Repository\PostRepository;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BlogController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    #[Route('/blog/{page}', name: 'blog')]
    public function index(int $page, PostRepository $postRepository): ResponseInterface
    {
        $posts = $postRepository->getPaginatedList($page);
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
