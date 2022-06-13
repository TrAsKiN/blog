<?php

namespace Blog\Form;

use Blog\Core\Authentication\UserProvider;
use Blog\Core\Form;
use Blog\Core\FormInterface;
use Blog\Core\Service\FlashService;
use Blog\Entity\User;
use Exception;
use PDOException;

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
        } catch (PDOException|Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
