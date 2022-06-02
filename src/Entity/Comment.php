<?php

namespace Blog\Entity;

use Blog\Core\Entity;
use DateTime;
use Exception;

class Comment extends Entity
{
    private int $id;
    private string $content;
    private ?DateTime $createdAt;
    private bool $valid = false;
    private int|User $author;

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

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
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
}
