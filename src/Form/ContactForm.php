<?php

namespace Blog\Form;

use Exception;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;

class ContactForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly FlashService $messages
    ) {
        $this->form->require([
            'required' => ['email', 'message'],
            'notEmpty' => ['email', 'message'],
        ]);
    }

    public function getResult(mixed $params = null): object|array|null
    {
        try {
            return $this->form->getData();
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
