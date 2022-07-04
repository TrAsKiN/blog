<?php

namespace Blog\Form;

use Blog\Entity\Post;
use Blog\Repository\PostRepository;
use Exception;
use Framework\Authentication\UserProvider;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;
use Framework\SlugTrait;

class PostForm implements FormInterface
{
    use SlugTrait;

    public function __construct(
        public readonly Form $form,
        private readonly FlashService $messages,
        private readonly PostRepository $repository,
        private readonly UserProvider $provider
    ) {
        $this->form->require([
            'required' => ['title', 'lede', 'content'],
            'notEmpty' => ['title', 'lede', 'content'],
        ]);
    }

    public function getResult(mixed $params = null): object|array|null
    {
        try {
            if ($params) {
                $post = $params;
                $post->setTitle($this->form->getData('title'));
                $post->setLede($this->form->getData('lede'));
                $post->setContent($this->form->getData('content'));
                $this->repository->update($post);
                return $post;
            } else {
                $post = new Post();
                $post->setAuthor($this->provider->getUser());
                $post->setTitle($this->form->getData('title'));
                $post->setSlug(self::slugify($this->form->getData('title')));
                $post->setLede($this->form->getData('lede'));
                $post->setContent($this->form->getData('content'));
                return $this->repository->find($this->repository->add($post));
            }
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
