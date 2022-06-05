<?php

namespace Blog\Core;

use Blog\Entity\User;
use Blog\Repository\UserRepository;

class UserProvider
{
    private User $user;
    private bool $isAuthenticated = false;

    public function __construct(
        private readonly UserRepository $userRepository
    ) {
        $this->user = new User();
    }

    public function retrieve(string $username): User|bool
    {
        $user = $this->userRepository->findByUsername($username);
        if (!$user instanceof User) {
            return false;
        }
        $this->setUser($user);
        $this->setIsAuthenticated(true);
        return $this->user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function setIsAuthenticated(bool $isAuthenticated): void
    {
        $this->isAuthenticated = $isAuthenticated;
    }
}
