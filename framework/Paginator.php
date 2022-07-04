<?php

namespace Framework;

use ArithmeticError;
use Blog\Entity\Comment;
use Blog\Entity\Post;
use DivisionByZeroError;
use Exception;

class Paginator extends Database
{
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
