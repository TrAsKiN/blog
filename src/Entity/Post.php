<?php

namespace Blog\Entity;

use DateTime;

class Post
{
    public function __construct(
        private readonly int $id,
        private string $title,
        private string $lede,
        private string $content,
        private DateTime $createdAt = new DateTime(),
        private DateTime $updatedAt = new DateTime()
    ) {
    }

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

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}