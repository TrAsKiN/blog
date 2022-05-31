<?php

namespace Blog\Repository;

use Blog\Core\Database;
use Blog\Entity\User;

class UserRepository extends Database
{
    public function findByUsername(string $username)
    {
        $statement = $this->pdo->prepare('SELECT * FROM `users` WHERE `username` = :username');
        $statement->bindParam('username', $username);
        $statement->execute();
        return $statement->fetchObject(User::class);
    }
}
