<?php

namespace Blog\Core;

class Session
{
    private array $session;

    public function __construct(
        public readonly string $id
    ) {
        session_id($id);
        session_start([
            'use_cookies' => false,
            'use_only_cookies' => true
        ]);
        $this->session = $_SESSION;
    }

    public function get(string $name): mixed
    {
        return $this->session[$name] ?? null;
    }

    public function set(string $name, mixed $value): void
    {
        $this->session[$name] = $value;
    }

    public function unset(string $name): void
    {
        unset($this->session[$name]);
    }

    public function erase(): void
    {
        $this->session = [];
    }

    public function persist(): void
    {
        $_SESSION = $this->session;
        session_write_close();
    }
}
