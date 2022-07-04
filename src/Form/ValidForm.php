<?php

namespace Blog\Form;

use Blog\Entity\Comment;
use Blog\Repository\CommentRepository;
use Exception;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;

class ValidForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly FlashService $messages,
        private readonly CommentRepository $repository
    ) {
        $this->form->require([
            'required' => ['valid'],
        ]);
    }

    public function getResult(mixed $params = null): object|array|null
    {
        try {
            if (!$params instanceof Comment) {
                throw new Exception("The parameter is not an instance of Comment!");
            }
            $comment = $params;
            if ($this->form->getData('valid') != $comment->getValid()) {
                $comment->setValid($this->form->getData('valid'));
            }
            if (!$this->repository->update($comment)) {
                throw new Exception("Unable to update the comment!");
            }
            return $comment;
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
