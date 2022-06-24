<?php

namespace Blog\Controller;

use ArithmeticError;
use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Paginator;
use Blog\Entity\Post;
use Blog\Core\Csrf;
use Blog\Core\Service\FlashService;
use Blog\Form\CommentForm;
use Blog\Repository\PostRepository;
use DivisionByZeroError;
use Exception;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class BlogController extends Controller
{
    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws PDOException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ArithmeticError
     * @throws DivisionByZeroError
     */
    #[Route('/blog/{page}', name: 'blog')]
    public function index(
        int $page,
        PostRepository $postRepository,
        Paginator $paginator
    ): ResponseInterface {
        $max = 12;
        $posts = $postRepository->getPaginatedList($page, $max);
        $numberOfPages = $paginator->getNumberOfPages(Post::class, $max);
        return $this->render('blog/list.html.twig', compact('posts', 'page', 'numberOfPages'));
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     * @throws PDOException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    #[Route('/blog/post/{slug}', name: 'post')]
    public function show(
        string $slug,
        PostRepository $postRepository,
        Csrf $csrf
    ): ResponseInterface {
        $post = $postRepository->findWithSlug($slug);
        $token = $csrf->new();
        return $this->render('post/show.html.twig', compact('post', 'token'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    #[Route('/blog/post/comment/{slug}', name: 'comment_add', restricted: true)]
    public function addComment(
        string $slug,
        CommentForm $commentForm,
        FlashService $messages
    ): ResponseInterface {
        if ($commentForm->form->isPost() && $commentForm->form->isValid()) {
            if ($commentForm->getResult($slug)) {
                $messages->addFlash(
                    "Votre commentaire a été enregistré ! Un administrateur le validera prochainement.",
                    'success'
                );
            }
        }
        return $this->redirect('post', compact('slug'));
    }
}
