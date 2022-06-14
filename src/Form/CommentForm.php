<?php

namespace Blog\Form;

use Blog\Core\Authentication\UserProvider;
use Blog\Core\Form;
use Blog\Core\FormInterface;
use Blog\Core\Service\FlashService;
use Blog\Repository\CommentRepository;
use Blog\Repository\PostRepository;
use Exception;

class CommentForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly FlashService $messages,
        private readonly PostRepository $postRepository,
        private readonly CommentRepository $commentRepository,
        private readonly UserProvider $provider
    ) {
        $this->form->require([
            'required' => ['content'],
            'notEmpty' => ['content'],
        ]);
    }

    public function getResult(mixed $params = null): object|array|null
    {
        try {
            $post = $this->postRepository->findWithSlug($params);
            $user = $this->provider->getUser();
            $this->commentRepository->addComment($post, $user, $this->form->getData('content'));
            return $post;
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
