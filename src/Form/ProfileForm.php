<?php

namespace Blog\Form;

use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use Framework\Authentication\PasswordEncoder;
use Framework\Authentication\UserProvider;
use Framework\Form;
use Framework\FormInterface;
use Framework\Service\FlashService;

class ProfileForm implements FormInterface
{
    public function __construct(
        public readonly Form $form,
        private readonly FlashService $messages,
        private readonly UserProvider $provider,
        private readonly PasswordEncoder $encoder,
        private readonly UserRepository $repository
    ) {
        $this->form->require([
            'required' => ['password'],
            'notEmpty' => ['password'],
            'length' => ['newPassword', null, 50],
            'isEquals' => ['newPassword', 'newPasswordConfirm'],
        ]);
    }

    public function getResult(mixed $params = null): ?User
    {
        try {
            $user = $this->provider->getUser();
            if (!$this->encoder->isPasswordValid($user->getPassword(), $this->form->getData('password'))) {
                throw new Exception("The password is not correct!");
            }
            if (!empty($this->form->getData('username'))) {
                $user->setUsername($this->form->getData('username'));
            }
            if (!empty($this->form->getData('email'))) {
                $user->setEmail($this->form->getData('email'));
            }
            if (!empty($this->form->getData('newPassword'))) {
                $user->setPassword($this->encoder->encodePassword($this->form->getData('newPassword')));
            }
            if (!$this->repository->update($user)) {
                throw new Exception("Unable to update the user!");
            }
            return $user;
        } catch (Exception $exception) {
            $this->messages->addFlash($exception->getMessage(), 'danger');
            return null;
        }
    }
}
