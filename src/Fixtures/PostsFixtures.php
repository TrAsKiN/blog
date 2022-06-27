<?php

namespace Blog\Fixtures;

use Blog\Core\Fixtures;
use Blog\Entity\Post;
use Blog\Repository\PostRepository;
use Blog\Repository\UserRepository;
use Exception;

class PostsFixtures extends Fixtures
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PostRepository $postRepository
    ) {
        parent::__construct();
    }

    public function load(): bool
    {
        try {
            $author = $this->userRepository->findByUsername('Admin');
        } catch (Exception) {
            return false;
        }
        for ($i = 1; $i <= 25; $i++) {
            $post = new Post();
            $post->setAuthor($author);
            $post->setTitle(ucfirst($this->faker->words(5, true)));
            $post->setSlug(self::slugify($post->getTitle()));
            $post->setLede($this->faker->sentence(20));
            $post->setContent($this->faker->paragraphs(4, true));
            try {
                $this->postRepository->add($post);
            } catch (Exception) {
                return false;
            }
        }
        return true;
    }
}
