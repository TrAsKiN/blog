<?php

namespace Blog\Core;

use ArithmeticError;
use Blog\Entity\Comment;
use Blog\Entity\Post;
use DivisionByZeroError;
use Exception;
use PDO;

class Paginator
{
    public function __construct(
        private readonly PDO $pdo
    ) {
    }

    /**
     * @throws DivisionByZeroError
     * @throws ArithmeticError
     * @throws Exception
     */
    public function getNumberOfPages(string $entity, int $maxPerPage): int
    {
        $request = match ($entity) {
            Post::class => $this->pdo->prepare('SELECT COUNT(*) FROM `posts`'),
            Comment::class => $this->pdo->prepare('SELECT COUNT(*) FROM `comments`'),
            default => null,
        };
        try {
            $request->execute();
        } catch (Exception $exception) {
            throw new Exception("The entity is not supported or is null!");
        }
        $total = $request->fetchColumn();
        return intdiv($total, $maxPerPage) + 1;
    }
}
