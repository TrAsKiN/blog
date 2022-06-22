<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Csrf;
use Blog\Core\Form;
use Blog\Core\Service\FlashService;
use Blog\Form\PostForm;
use Blog\Form\ValidForm;
use Blog\Repository\CommentRepository;
use Blog\Repository\PostRepository;
use Exception;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CommentsController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws PDOException
     * @throws Exception
     */
    #[Route('/admin/comments/list', name: 'admin_comments_list', roles: ['admin'], restricted: true)]
    public function list(
        CommentRepository $repository,
        Csrf $csrf
    ): ResponseInterface {
        $comments = $repository->getPaginatedList(1, 100);
        $token = $csrf->new();
        return $this->render('admin/comments/list.html.twig', compact('comments', 'token'));
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws PDOException
     * @throws LoaderError
     * @throws Exception
     */
    #[Route('/admin/comments/edit/{id}', name: 'admin_comments_edit', roles: ['admin'], restricted: true)]
    public function edit(
        int $id,
        CommentRepository $repository,
        ValidForm $commentForm,
        FlashService $messages,
        Csrf $csrf
    ): ResponseInterface {
        $comment = $repository->find($id);
        if ($commentForm->form->isPost() && $commentForm->form->isValid()) {
            if ($commentForm->getResult($comment)) {
                $messages->addFlash("Commentaire mis à jour !", 'success');
            }
        }
        $token = $csrf->new();
        return $this->render('admin/comments/edit.html.twig', compact('comment', 'token'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws PDOException
     * @throws Exception
     */
    #[Route('/admin/comments/delete/{id}', name: 'admin_comments_delete', roles: ['admin'], restricted: true)]
    public function delete(
        int $id,
        CommentRepository $repository,
        Form $form,
        FlashService $messages
    ): ResponseInterface {
        $comment = $repository->find($id);
        if ($form->isPost() && $form->isValid()) {
            try {
                $repository->delete($comment);
                $messages->addFlash("Commentaire supprimé !", 'warning');
            } catch (Exception $exception) {
                $messages->addFlash($exception->getMessage(), 'danger');
            }
        }
        return $this->redirect('admin_comments_list');
    }
}
