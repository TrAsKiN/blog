<?php

namespace Blog\Controller;

use Blog\Core\Controller;
use Blog\Core\Route;
use Blog\Repository\PostRepository;
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
