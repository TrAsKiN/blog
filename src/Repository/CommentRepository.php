<?php

namespace Blog\Repository;

use Blog\Entity\Comment;
use Blog\Entity\Post;
use Blog\Entity\User;
use Framework\Database;
use PDO;
use PDOException;

class CommentRepository extends Database
{
    /**
     * @throws PDOException
     */
    public function find(int $id): mixed
    {
        $commentStatement = $this->pdo->prepare(
            'SELECT * FROM `comments` WHERE `id` = :id'
        );
        $commentStatement->execute([
            'id' => $id,
        ]);
        $comment = $commentStatement->fetchObject(Comment::class);
        $this->setAuthorAndPost($comment);
        return $comment;
    }

    /**
     * @throws PDOException
     */
    public function getPaginatedList(int $page, int $max = 10): bool|array
    {
        $offset = $max * ($page - 1);
        $commentsStatement = $this->pdo->prepare(
            "SELECT * FROM `comments` ORDER BY `created_at` DESC LIMIT $max OFFSET $offset"
        );
        $commentsStatement->execute();
        $comments = $commentsStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);
        foreach ($comments as $comment) {
            $this->setAuthorAndPost($comment);
        }
        return $comments;
    }

    /**
     * @throws PDOException
     */
    public function getCommentsToValidate(): bool|array
    {
        $commentsStatement = $this->pdo->prepare(
            'SELECT * FROM `comments` WHERE `valid` = :valid ORDER BY `created_at`'
        );
        $commentsStatement->execute([
            'valid' => Comment::PENDING,
        ]);
        $comments = $commentsStatement->fetchAll(PDO::FETCH_CLASS, Comment::class);
        foreach ($comments as $comment) {
            $this->setAuthorAndPost($comment);
        }
        return $comments;
    }

    /**
     * @throws PDOException
     */
    public function update(Comment $comment): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE `comments` SET
                `content` = :content,
                `valid` = :valid
                WHERE `id` = :id'
        );
        return $statement->execute([
            'content' => $comment->getContent(),
            'valid' => $comment->getValid(),
            'id' => $comment->getId(),
        ]);
    }

    /**
     * @throws PDOException
     */
    public function add(Post $post, User $author, string $content): bool|string
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO `comments` (`author`, `post`, `content`) VALUES (:author, :post, :content)'
        );
        $statement->execute([
            'author' => $author->getId(),
            'post' => $post->getId(),
            'content' => $content,
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * @throws PDOException
     */
    public function delete(Comment $comment): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM `comments` WHERE `id` = :id'
        );
        return $statement->execute([
            'id' => $comment->getId(),
        ]);
    }

    /**
     * @throws PDOException
     */
    private function setAuthorAndPost(Comment $comment): void
    {
        $authorStatement = $this->pdo->prepare(
            'SELECT * FROM `users` WHERE `id` = :author'
        );
        $authorStatement->execute([
            'author' => $comment->getAuthor(),
        ]);
        $postStatement = $this->pdo->prepare('SELECT * FROM `posts` WHERE `id` = :id');
        $postStatement->execute([
            'id' => $comment->getPost(),
        ]);
        $comment->setAuthor($authorStatement->fetchObject(User::class));
        $comment->setPost($postStatement->fetchObject(Post::class));
    }
}
