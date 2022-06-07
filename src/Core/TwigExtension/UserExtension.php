<?php

namespace Blog\Core\TwigExtension;

use Blog\Core\Authentication\UserProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserExtension extends AbstractExtension
{
    public function __construct(
        private readonly UserProvider $provider
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'user',
                [$this->provider, 'getUser']
            ),
            new TwigFunction(
                'isAuthenticated',
                [$this->provider, 'isAuthenticated']
            )
        ];
    }
}
