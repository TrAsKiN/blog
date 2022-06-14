<?php

namespace Blog\Controller;

use Blog\Core\Attribute\Route;
use Blog\Core\Controller;
use Blog\Core\Service\FlashService;
use Blog\Repository\CommentRepository;
use InvalidArgumentException;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminController extends Controller
{
    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     * @throws PDOException
     */
    #[Route('/admin', name: 'admin_dashboard', roles: ['admin'], restricted: true)]
    public function dashboard(
        CommentRepository $commentRepository
    ): ResponseInterface {
        $comments = $commentRepository->getCommentsToValidate();
        return $this->render('admin/dashboard.html.twig', compact('comments'));
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws InvalidArgumentException
     * @throws LoaderError
     * @throws PDOException
     */
    #[Route('/admin/blog/comments', name: 'admin_comments', roles: ['admin'], restricted: true)]
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
    #[Route('/admin/blog/comment/{id}', name: 'comment_valid', roles: ['admin'], restricted: true)]
    public function validComment(
        int $id,
        CommentRepository $commentRepository,
        FlashService $messages
    ): ResponseInterface {
        $comment = $commentRepository->find($id);
        $comment->setValid(true);
        if ($commentRepository->updateComment($comment)) {
            $messages->addFlash("Commentaire validé !", 'success');
        }
        return $this->redirect('admin_comments');
    }
}
