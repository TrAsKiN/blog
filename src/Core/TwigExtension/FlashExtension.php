<?php

namespace Blog\Core\TwigExtension;

use Blog\Core\FlashMessages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashExtension extends AbstractExtension
{
    public function __construct(
        private readonly FlashMessages $messages
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', [$this, 'getFlashMessages']),
        ];
    }

    public function getFlashMessages(): mixed
    {
        return $this->messages->retrieveMessage();
    }
}
