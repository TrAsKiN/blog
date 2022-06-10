<?php

namespace Blog\Core\Authentication;

use Blog\Entity\User;
use Blog\Repository\UserRepository;
use Exception;
use PDOException;

class UserProvider
{
    private ?User $user;
    private bool $isAuthenticated = false;

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PasswordEncoder $encoder
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

    /**
     * @throws PDOException
     * @throws Exception
     */
    public function login(string $email, string $password): User
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user instanceof User) {
            throw new Exception("User does not exist!");
        }
        if (!$this->encoder->isPasswordValid($user->getPassword(), $password)) {
            throw new Exception("Password is not correct!");
        }
        $token = $this->encoder->createToken();
        if (!$this->userRepository->setToken($user, $token)) {
            throw new Exception("Unable to modify user's token");
        }
        $user->setToken($token);
        return $user;
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
