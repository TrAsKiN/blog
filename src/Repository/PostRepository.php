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
    public function find(int $id): ?Post
    {
        $postStatement = $this->pdo->prepare('SELECT * FROM `posts` WHERE `id` = :id');
        $postStatement->execute([
            'id' => $id,
        ]);
        $post = $postStatement->fetchObject(Post::class);
        $this->setAuthorAndComments($post);
        return $post;
    }

    /**
     * @throws PDOException
     */
    public function findWithSlug(string $slug): ?Post
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
    public function updatePost(Post $post): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE `posts` SET
                `title` = :title,
                `lede` = :lede,
                `content` = :content,
                `updated_at` = NOW()
                WHERE `id` = :id'
        );
        return $statement->execute([
            'title' => $post->getTitle(),
            'lede' => $post->getLede(),
            'content' => $post->getContent(),
            'id' => $post->getId(),
        ]);
    }

    /**
     * @throws PDOException
     */
    public function addPost(Post $post): bool|string
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO `posts` (`author`, `title`, `slug`, `lede`, `content`)
                    VALUES (:author, :title, :slug, :lede, :content)'
        );
        $statement->execute([
            'author' => $post->getAuthor()->getId(),
            'title' => $post->getTitle(),
            'slug' => $post->getSlug(),
            'lede' => $post->getLede(),
            'content' => $post->getContent(),
        ]);
        return $this->pdo->lastInsertId();
    }

    /**
     * @throws PDOException
     */
    public function deletePost(Post $post): bool
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM `posts` WHERE `id` = :id'
        );
        return $statement->execute([
            'id' => $post->getId(),
        ]);
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
            'SELECT * FROM `comments` WHERE `post` = :post AND `valid` = :valid ORDER BY `created_at` DESC'
        );
        $commentsStatement->execute([
            'post' => $post->getId(),
            'valid' => Comment::VALIDATED,
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
