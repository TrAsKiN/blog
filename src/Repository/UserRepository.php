<?php

namespace Blog\Repository;

use Blog\Core\Database;
use Blog\Entity\User;
use PDOException;

class UserRepository extends Database
{
    /**
     * @throws PDOException
     */
    public function findByUsername(string $username): mixed
    {
        $statement = $this->pdo->prepare('SELECT * FROM `users` WHERE `username` = :username');
        $statement->execute([
            'username' => $username,
        ]);
        return $statement->fetchObject(User::class);
    }

    /**
     * @throws PDOException
     */
    public function findByEmail(string $email): mixed
    {
        $statement = $this->pdo->prepare('SELECT * FROM `users` WHERE `email` = :email');
        $statement->execute([
            'email' => $email,
        ]);
        return $statement->fetchObject(User::class);
    }

    /**
     * @throws PDOException
     */
    public function findByToken(string $token): mixed
    {
        $statement = $this->pdo->prepare('SELECT * FROM `users` WHERE `token` = :token');
        $statement->execute([
            'token' => $token,
        ]);
        return $statement->fetchObject(User::class);
    }

    /**
     * @throws PDOException
     */
    public function setToken(User $user, string $token): bool
    {
        $statement = $this->pdo->prepare('UPDATE `users` SET `token` = :token WHERE `id` = :id');
        return $statement->execute([
            'token' => $token,
            'id' => $user->getId(),
        ]);
    }

    /**
     * @throws PDOException
     */
    public function updateUser(User $user): bool
    {
        $statement = $this->pdo->prepare(
            'UPDATE `users` SET
                `username` = :username,
                `email` = :email,
                `password` = :password,
                `roles` = :roles,
                `active` = :active,
                `token` = :token
                WHERE `id` = :id'
        );
        return $statement->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roles' => json_encode($user->getRoles()),
            'active' => (int) $user->isActive(),
            'token' => $user->getToken(),
            'id' => $user->getId(),
        ]);
    }

    /**
     * @throws PDOException
     */
    public function addUser(User $user): bool|string
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO `users` (`username`, `email`, `password`, `roles`, `active`, `token`)
                    VALUES (:username, :email, :password, :roles, :active, :token)'
        );
        $statement->execute([
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'roles' => json_encode($user->getRoles()),
            'active' => (int) $user->isActive(),
            'token' => $user->getToken(),
        ]);
        return $this->pdo->lastInsertId();
    }
}
