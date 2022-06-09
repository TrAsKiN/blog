<?php

namespace Blog\Core\Authentication;

use Blog\Entity\User;
use Blog\Repository\UserRepository;
use PDOException;

class UserProvider
{
    private ?User $user;
    private bool $isAuthenticated = false;

    public function __construct(
        private readonly UserRepository $userRepository
    ) {
    }

    /**
     * @throws PDOException
     */
    public function retrieve(string $username): User|bool
    {
        $user = $this->userRepository->findByUsername($username);
        if (!$user instanceof User) {
            return false;
        }
        $this->setUser($user);
        return $this->user;
    }

    public function getUser(): ?User
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

    public function setAuthenticated(bool $isAuthenticated): void
    {
        $this->isAuthenticated = $isAuthenticated;
    }
}
