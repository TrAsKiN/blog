<?php

namespace Blog\Form;

use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use Framework\Authentication\PasswordEncoder;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;

class ForgottenForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly UserRepository $repository,
        private readonly FlashService $messages,
        private readonly PasswordEncoder $encoder
    ) {
        $this->form->require([
            'required' => ['email'],
            'notEmpty' => ['email'],
        ]);
    }

    public function getResult(mixed $params = null): ?User
    {
        try {
            $user = $this->repository->findByEmail($this->form->getData('email'));
            if ($user instanceof User) {
                $user->setToken($this->encoder->createToken());
                $this->repository->update($user);
            }
            $this->messages->addFlash(
                "Si cette adresse est associé à un compte, vous allez recevoir un mail pour changer votre mot de passe",
                'success'
            );
            return $user;
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
