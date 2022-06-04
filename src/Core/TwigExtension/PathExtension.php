<?php

namespace Blog\Core\TwigExtension;

use Blog\Core\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PathExtension extends AbstractExtension
{
    public function __construct(
        private readonly Router $router
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path', [$this->router, 'generateUri']),
        ];
    }
}
