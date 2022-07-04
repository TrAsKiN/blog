<?php

namespace Blog\Form;

use Blog\Entity\User;
use Exception;
use Framework\Authentication\UserProvider;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;

class LoginForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly UserProvider $provider,
        private readonly FlashService $messages
    ) {
        $this->form->require([
            'required' => ['email', 'password'],
            'notEmpty' => ['email', 'password'],
        ]);
    }

    public function getResult(mixed $params = null): ?User
    {
        try {
            return $this->provider->login(
                $this->form->getData('email'),
                $this->form->getData('password')
            );
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
