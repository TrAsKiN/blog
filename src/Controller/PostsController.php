<?php

namespace Blog\Controller;

use ArithmeticError;
use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Csrf;
use Blog\Core\Form;
use Blog\Core\Paginator;
use Blog\Core\Service\FlashService;
use Blog\Entity\Post;
use Blog\Form\PostForm;
use Blog\Repository\PostRepository;
use DivisionByZeroError;
use Exception;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PostsController extends Controller
{
    /**
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws PDOException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws ArithmeticError
     * @throws DivisionByZeroError
     * @throws Exception
     */
    #[Route('/admin/posts/list/{page}', name: 'admin_posts_list', roles: ['admin'], restricted: true)]
    public function list(
        int $page,
        PostRepository $repository,
        Csrf $csrf,
        Paginator $paginator
    ): ResponseInterface {
        $max = 20;
        $posts = $repository->getPaginatedList($page, $max);
        $numberOfPages = $paginator->getNumberOfPages(Post::class, $max);
        $token = $csrf->new();
        return $this->render('admin/posts/list.html.twig', compact(
            'posts',
            'token',
            'page',
            'numberOfPages'
        ));
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws PDOException
     * @throws LoaderError
     * @throws Exception
     */
    #[Route('/admin/posts/edit/{id}', name: 'admin_posts_edit', roles: ['admin'], restricted: true)]
    public function edit(
        int $id,
        PostRepository $repository,
        PostForm $postForm,
        FlashService $messages,
        Csrf $csrf
    ): ResponseInterface {
        $post = $repository->find($id);
        if ($postForm->form->isPost() && $postForm->form->isValid()) {
            if ($postForm->getResult($post)) {
                $messages->addFlash("Article mis à jour !", 'success');
            }
        }
        $token = $csrf->new();
        return $this->render('admin/posts/edit.html.twig', compact(
            'post',
            'token'
        ));
    }

    /**
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    #[Route('/admin/posts/delete/{id}', name: 'admin_posts_delete', roles: ['admin'], restricted: true)]
    public function delete(
        int $id,
        PostRepository $repository,
        Form $form,
        FlashService $messages
    ): ResponseInterface {
        $post = $repository->find($id);
        if ($form->isPost() && $form->isValid()) {
            try {
                $repository->delete($post);
                $messages->addFlash("Article supprimé !", 'warning');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->redirect('admin_posts_list', [
            'page' => 1,
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    #[Route('/admin/posts/new', name: 'admin_posts_new', roles: ['admin'], restricted: true)]
    public function new(
        PostForm $postForm,
        FlashService $messages,
        Csrf $csrf
    ): ResponseInterface {
        if ($postForm->form->isPost() && $postForm->form->isValid()) {
            if ($postForm->getResult()) {
                $messages->addFlash("Article ajouté !", 'success');
                return $this->redirect('admin_posts_list', [
                    'page' => 1,
                ]);
            }
        }
        $token = $csrf->new();
        return $this->render('admin/posts/new.html.twig', compact(
            'token'
        ));
    }
}
