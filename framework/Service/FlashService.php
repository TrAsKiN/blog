<?php

namespace Framework\Service;

class FlashService
{
    private array $messages = [];

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    public function addFlash(string $message, string $type = 'secondary'): void
    {
        $this->messages[][$type] = $message;
    }

    public function retrieveMessage(): mixed
    {
        return array_shift($this->messages);
    }
}
