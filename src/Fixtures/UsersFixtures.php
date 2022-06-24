<?php

namespace Blog\Fixtures;

use Blog\Core\Authentication\PasswordEncoder;
use Blog\Core\Fixtures;
use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;

class UsersFixtures extends Fixtures
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordEncoder $encoder
    ) {
        parent::__construct();
    }

    public function load(): bool
    {
        try {
            $admin = new User();
            $admin->setUsername('Admin');
            $admin->setEmail('admin@localhost');
            $admin->setPassword($this->encoder->encodePassword('admin'));
            $admin->setRoles(["admin"]);
            $admin->setActive(true);
            $user = new User();
            $user->setUsername('User');
            $user->setEmail('user@localhost');
            $user->setPassword($this->encoder->encodePassword('user'));
            $user->setActive(true);
            $this->userRepository->add($admin);
            $this->userRepository->add($user);
        } catch (Exception) {
            return false;
        }
        return true;
    }
}
