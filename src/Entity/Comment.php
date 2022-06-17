<?php

namespace Blog\Entity;

use Blog\Core\Entity;
use DateTime;
use Exception;

class Comment extends Entity
{
    public const PENDING = 0;
    public const VALIDATED = 1;
    public const DELETED = 2;

    private int $id;
    private string $content;
    private ?DateTime $createdAt;
    private int $valid = self::PENDING;
    private int|User $author;
    private int|Post $post;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @throws Exception
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = new DateTime($createdAt);
    }

    public function getValid(): int
    {
        return $this->valid;
    }

    public function setValid(int $valid): void
    {
        $this->valid = $valid;
    }

    public function getAuthor(): User|int
    {
        return $this->author;
    }

    public function setAuthor(User|int $author): void
    {
        $this->author = $author;
    }

    public function getPost(): int|Post
    {
        return $this->post;
    }

    public function setPost(int|Post $post): void
    {
        $this->post = $post;
    }
}
