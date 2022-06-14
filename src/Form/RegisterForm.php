<?php

namespace Blog\Form;

use Blog\Core\Authentication\PasswordEncoder;
use Blog\Core\Form;
use Blog\Core\FormInterface;
use Blog\Core\Service\FlashService;
use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use PDOException;

class RegisterForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly UserRepository $repository,
        private readonly FlashService $messages,
        private readonly PasswordEncoder $encoder
    ) {
        $this->form->require([
            'required' => ['email', 'username', 'password', 'passwordConfirm'],
            'notEmpty' => ['email', 'username', 'password', 'passwordConfirm'],
            'length' => ['password', null, 50],
            'isEquals' => ['password', 'passwordConfirm'],
        ]);
    }

    public function getResult(mixed $params = null): ?User
    {
        try {
            $user = new User();
            $user->setUsername($this->form->getData('username'));
            $user->setEmail($this->form->getData('email'));
            $user->setPassword($this->encoder->encodePassword($this->form->getData('password')));
            $user->setToken($this->encoder->createToken());
            if (!$this->repository->addUser($user)) {
                throw new Exception("Unable to register user");
            }
            return $user;
        } catch (PDOException|Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
