<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Form;
use Blog\Core\Service\FlashService;
use Blog\Entity\Post;
use Blog\Form\PostForm;
use Blog\Repository\PostRepository;
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CrudController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws PDOException
     * @throws LoaderError
     */
    #[Route('/admin/blog/list', name: 'admin_blog_list', roles: ['admin'], restricted: true)]
    public function list(
        PostRepository $repository
    ): ResponseInterface {
        $posts = $repository->getPaginatedList(1);
        return $this->render('admin/list.html.twig', compact('posts'));
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws PDOException
     * @throws LoaderError
     */
    #[Route('/admin/blog/edit/{id}', name: 'admin_blog_edit', roles: ['admin'], restricted: true)]
    public function edit(
        int $id,
        PostRepository $repository,
        PostForm $postForm,
        FlashService $messages
    ): ResponseInterface {
        $post = $repository->find($id);
        if ($postForm->form->isPost() && $postForm->form->isValid()) {
            if ($postForm->getResult($post)) {
                $messages->addFlash("Article mis à jour !", 'success');
            }
        }
        return $this->render('admin/edit.html.twig', compact('post'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    #[Route('/admin/blog/delete/{id}', name: 'admin_blog_delete', roles: ['admin'], restricted: true)]
    public function delete(
        int $id,
        PostRepository $repository,
        Form $form,
        FlashService $messages
    ): ResponseInterface {
        $post = $repository->find($id);
        if ($form->isPost() && $form->isValid()) {
            try {
                $repository->deletePost($post);
                $messages->addFlash("Article supprimé !", 'warning');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->redirect('admin_blog_list');
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     */
    #[Route('/admin/blog/new', name: 'admin_blog_new', roles: ['admin'], restricted: true)]
    public function new(
        PostForm $postForm,
        FlashService $messages
    ): ResponseInterface {
        if ($postForm->form->isPost() && $postForm->form->isValid()) {
            if ($postForm->getResult()) {
                $messages->addFlash("Article ajouté !", 'success');
                return $this->redirect('admin_blog_list');
            }
        }
        return $this->render('admin/new.html.twig');
    }
}
