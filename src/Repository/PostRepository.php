<?php

namespace Blog\Repository;

use Blog\Core\Database;
use Blog\Entity\Comment;
use Blog\Entity\Post;
use Blog\Entity\User;
use PDO;
use PDOException;

class PostRepository extends Database
{
    /**
     * @throws PDOException
     */
    public function getPaginatedList(int $page, int $max = 10): bool|array
    {
        $offset = $max * ($page - 1);
        $postsStatement = $this->pdo->prepare(
            "SELECT * FROM `posts` ORDER BY `created_at` DESC LIMIT $max OFFSET $offset"
        );
        $postsStatement->execute();
        $posts = $postsStatement->fetchAll(PDO::FETCH_CLASS, Post::class);
        foreach ($posts as $post) {
            $this->setAuthorAndComments($post);
        }
        return $posts;
    }

    /**
     * @throws PDOException
     */
    public function findWithSlug(string $slug)
    {
        $postStatement = $this->pdo->prepare('SELECT * FROM `posts` WHERE `slug` = :slug');
        $postStatement->execute([
            'slug' => $slug,
        ]);
        $post = $postStatement->fetchObject(Post::class);
        $this->setAuthorAndComments($post);
        return $post;
    }

    /**
     * @throws PDOException
     */
    private function setAuthorAndComments(Post $post): void
    {
        $authorStatement = $this->pdo->prepare(
            'SELECT * FROM `users` WHERE `id` = :author'
        );
        $authorStatement->execute([
            'author' => $post->getAuthor(),
        ]);
        $post->setAuthor($authorStatement->fetchObject(User::class));
        $commentsStatement = $this->pdo->prepare(
            'SELECT * FROM `comments` WHERE `post` = :post AND `valid` = 1 ORDER BY `created_at` DESC'
        );
        $commentsStatement->execute([
            'post' => $post->getId(),
        ]);
        $comments = $commentsStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);
        foreach ($comments as $comment) {
            $commentAuthorStatement = $this->pdo->prepare(
                'SELECT * FROM `users` WHERE `id` = :author'
            );
            $commentAuthorStatement->execute([
                'author' => $comment->getAuthor(),
            ]);
            $comment->setAuthor($commentAuthorStatement->fetchObject(User::class));
        }
        $post->setComments($comments);
    }
}
