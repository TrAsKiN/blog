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

class PostController extends Controller
{
    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws PDOException
     * @throws InvalidArgumentException
     */
    #[Route('/blog/post/{slug}', name: 'post')]
    public function index(string $slug, PostRepository $postRepository): ResponseInterface
    {
        $post = $postRepository->findWithSlug($slug);
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
