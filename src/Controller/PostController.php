<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Csrf;
use Blog\Repository\PostRepository;
use Exception;
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
     * @throws Exception
     */
    #[Route('/blog/post/{slug}', name: 'post')]
    public function index(
        string $slug,
        PostRepository $postRepository,
        Csrf $csrf
    ): ResponseInterface {
        $post = $postRepository->findWithSlug($slug);
        $token = $csrf->new();
        return $this->render('post/show.html.twig', compact('post', 'token'));
    }
}
