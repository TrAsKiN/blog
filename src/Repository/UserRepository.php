<?php

namespace Blog\Repository;

use Blog\Core\Database;
use Blog\Entity\User;

class UserRepository extends Database
{
    public function findByUsername(string $username): mixed
    {
        $statement = $this->pdo->prepare('SELECT * FROM `users` WHERE `username` = :username');
        $statement->execute([
            'username' => $username,
        ]);
        return $statement->fetchObject(User::class);
    }

    public function findByEmail(string $email): mixed
    {
        $statement = $this->pdo->prepare('SELECT * FROM `users` WHERE `email` = :email');
        $statement->execute([
            'email' => $email,
        ]);
        return $statement->fetchObject(User::class);
    }

    public function setToken(int $id, string $token): bool
    {
        $statement = $this->pdo->prepare('UPDATE `users` SET `token` = :token WHERE `id` = :id');
        return $statement->execute([
            'token' => $token,
            'id' => $id,
        ]);
    }

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
            'active' => $user->isActive(),
            'token' => $user->getToken(),
        ]);
        return $this->pdo->lastInsertId();
    }
}
