<?php

namespace Framework\Authentication;

use Exception;
use Framework\TokenTrait;

class PasswordEncoder
{
    use TokenTrait;

    public function __construct(
        private readonly int $cost = 10,
        private readonly int $maxLength = 50
    ) {
    }

    /**
     * @throws Exception
     */
    public function encodePassword(string $rawPassword): string
    {
        if (strlen($rawPassword) > $this->maxLength) {
            throw new Exception("Password length is too long!");
        }
        return password_hash($rawPassword, PASSWORD_BCRYPT, ['cost' => $this->cost]);
    }

    public function isPasswordValid(string $encodedPassword, string $rawPassword): bool
    {
        return password_verify($rawPassword, $encodedPassword);
    }
}
