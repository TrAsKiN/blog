<?php

namespace Blog\Core;

class FlashMessages
{
    private array $flashMessages = [];

    public function getMessages(): array
    {
        return $this->flashMessages;
    }

    public function setMessages(array $flashMessages): void
    {
        $this->flashMessages = $flashMessages;
    }

    public function addFlash(string $message, string $type = 'secondary'): void
    {
        $this->flashMessages[][$type] = $message;
    }

    public function retrieveMessage(): mixed
    {
        return array_shift($this->flashMessages);
    }
}
