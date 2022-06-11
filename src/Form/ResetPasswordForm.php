<?php

namespace Blog\Form;

use Blog\Core\Authentication\PasswordEncoder;
use Blog\Core\Form;
use Blog\Core\Service\FlashService;
use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use PDOException;

class ResetPasswordForm
{
    public function __construct(
        public readonly Form $form,
        private readonly UserRepository $repository,
        private readonly FlashService $messages,
        private readonly PasswordEncoder $encoder
    ) {
        $this->form->require([
            'required' => ['password', 'passwordConfirm'],
            'notEmpty' => ['password', 'passwordConfirm'],
            'length' => ['password', null, 50],
            'isEquals' => ['password', 'passwordConfirm'],
        ]);
    }

    public function getResult(string $token): ?User
    {
        try {
            $user = $this->repository->findByToken($token);
            if (!$user instanceof User) {
                throw new Exception("Aucune correspondance avec un utilisateur enregistré !");
            }
            $user->setPassword($this->encoder->encodePassword($this->form->getData('password')));
            $user->setToken(null);
            if (!$this->repository->updateUser($user)) {
                throw new Exception("Impossible de mettre à jour les informations !");
            }
            return $user;
        } catch (PDOException|Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
