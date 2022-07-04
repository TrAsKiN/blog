<?php

namespace Framework\TwigExtension;

use Framework\Authentication\UserProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RoleExtension extends AbstractExtension
{
    public function __construct(
        private readonly UserProvider $provider
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('hasRole', [$this, 'hasRole']),
        ];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->provider->getUser()->getRoles());
    }
}
