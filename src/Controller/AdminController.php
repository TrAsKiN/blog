<?php

namespace Blog\Controller;

use Blog\Repository\CommentRepository;
use Framework\Attribute\Route;
use Framework\Controller;
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
}
