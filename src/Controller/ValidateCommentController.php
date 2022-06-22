<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Service\FlashService;
use Blog\Entity\Comment;
use Blog\Repository\CommentRepository;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ValidateCommentController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws PDOException
     */
    #[Route('/admin/comments', name: 'admin_comments', roles: ['admin'], restricted: true)]
    public function comments(
        CommentRepository $commentRepository
    ): ResponseInterface {
        $comments = $commentRepository->getCommentsToValidate();
        return $this->render('admin/comments.html.twig', compact('comments'));
    }

    /**
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    #[Route('/admin/comments/valid/{id}', name: 'comment_valid', roles: ['admin'], restricted: true)]
    public function validComment(
        int $id,
        CommentRepository $commentRepository,
        FlashService $messages
    ): ResponseInterface {
        $comment = $commentRepository->find($id);
        $comment->setValid(Comment::VALIDATED);
        if ($commentRepository->update($comment)) {
            $messages->addFlash("Commentaire validé !", 'success');
        }
        return $this->redirect('admin_comments');
    }

    /**
     * @throws InvalidArgumentException
     * @throws PDOException
     */
    #[Route('/admin/comments/invalid/{id}', name: 'comment_invalid', roles: ['admin'], restricted: true)]
    public function invalidComment(
        int $id,
        CommentRepository $commentRepository,
        FlashService $messages
    ): ResponseInterface {
        $comment = $commentRepository->find($id);
        $comment->setValid(Comment::DELETED);
        if ($commentRepository->update($comment)) {
            $messages->addFlash("Commentaire supprimé !", 'success');
        }
        return $this->redirect('admin_comments');
    }
}
