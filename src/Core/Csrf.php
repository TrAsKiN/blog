<?php

namespace Blog\Core;

use Exception;

class Csrf
{
    use TokenTrait;

    const KEY = '_csrf';

    private array $tokens = [];

    public function __construct(
        private readonly Session $session
    ) {
        if ($tokens = $this->session->get(self::KEY)) {
            $this->tokens = $tokens;
        }
    }

    public function exist(string $token): bool
    {
        if (in_array($token, $this->tokens)) {
            return true;
        }
        return false;
    }

    /**
     * @throws Exception
     */
    public function new(): string
    {
        $token = $this->createToken();
        $this->tokens[] = $token;
        $this->session->set(self::KEY, $this->tokens);
        return $token;
    }
}
