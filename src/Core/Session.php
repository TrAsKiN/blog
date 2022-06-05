<?php

namespace Blog\Core;

class Session
{
    private string $id;
    private array $session;

    public function __toString(): string
    {
        return json_encode($this->session);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function open(string $id): void
    {
        $this->id = $id;
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

    public function persist(): void
    {
        $_SESSION = $this->session;
        session_write_close();
    }
}
