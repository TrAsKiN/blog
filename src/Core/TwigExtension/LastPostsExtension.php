<?php

namespace Blog\Core\TwigExtension;

use Blog\Repository\PostRepository;
use PDOException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LastPostsExtension extends AbstractExtension
{
    public function __construct(
        private readonly PostRepository $postRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('lastPosts', [$this, 'lastPosts']),
        ];
    }

    /**
     * @throws PDOException
     */
    public function lastPosts(int $number): bool|array
    {
        return $this->postRepository->getPaginatedList(1, $number);
    }
}
