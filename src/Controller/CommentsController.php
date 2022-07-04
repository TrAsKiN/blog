<?php

namespace Blog\Controller;

use ArithmeticError;
use Blog\Entity\Comment;
use Blog\Form\ValidForm;
use Blog\Repository\CommentRepository;
use DivisionByZeroError;
use Exception;
use Framework\Attribute\Route;
use Framework\Controller;
use Framework\Csrf;
use Framework\Form;
use Framework\Paginator;
use Framework\Service\FlashService;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class CommentsController extends Controller
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
    #[Route('/admin/comments/list/{page}', name: 'admin_comments_list', roles: ['admin'], restricted: true)]
    public function list(
        int $page,
        CommentRepository $repository,
        Csrf $csrf,
        Paginator $paginator
    ): ResponseInterface {
        $max = 20;
        $comments = $repository->getPaginatedList($page, $max);
        $numberOfPages = $paginator->getNumberOfPages(Comment::class, $max);
        $token = $csrf->new();
        return $this->render('admin/comments/list.html.twig', compact(
            'comments',
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
        return $this->render('admin/comments/edit.html.twig', compact(
            'comment',
            'token'
        ));
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
        return $this->redirect('admin_comments_list', [
            'page' => 1,
        ]);
    }
}
