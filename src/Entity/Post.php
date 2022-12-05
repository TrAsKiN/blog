<?php

namespace Blog\Entity;

use DateTime;
use Exception;
use Framework\Entity;

class Post extends Entity
{
    private readonly int $id;
    private string $slug;
    private string $title;
    private string $lede;
    private string $content;
    private ?DateTime $createdAt;
    private ?DateTime $updatedAt;
    private int|User $author;
    private array|int $comments = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLede(): string
    {
        return $this->lede;
    }

    public function setLede(string $lede): void
    {
        $this->lede = $lede;
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
    public function setCreatedAt(mixed $createdAt): void
    {
        $this->createdAt = new DateTime($createdAt);
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @throws Exception
     */
    public function setUpdatedAt(mixed $updatedAt): void
    {
        $this->updatedAt = new DateTime($updatedAt);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {

            $this->slug = $slug;
    }

    public function getAuthor(): User|int
    {
        return $this->author;
    }

    public function setAuthor(User|int $author): void
    {
        $this->author = $author;
    }

    public function getComments(): array|int
    {
        return $this->comments;
    }

    public function setComments(array|int $comments): void
    {
        $this->comments = $comments;
    }
}
