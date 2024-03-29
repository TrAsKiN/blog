<?php

namespace Blog\Form;

use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use Framework\Authentication\PasswordEncoder;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;

class ResetPasswordForm implements FormInterface
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

    public function getResult(mixed $params = null): ?User
    {
        try {
            $user = $this->repository->findByToken($params);
            if (!$user instanceof User) {
                throw new Exception("Aucune correspondance avec un utilisateur enregistré !");
            }
            $user->setPassword($this->encoder->encodePassword($this->form->getData('password')));
            $user->setToken(null);
            if (!$this->repository->update($user)) {
                throw new Exception("Impossible de mettre à jour les informations !");
            }
            return $user;
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
