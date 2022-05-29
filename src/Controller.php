<?php

namespace Blog;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFunction;

class Controller
{
    public function __construct(
        private readonly Environment $twig,
        private readonly Container $container,
        private readonly Router $router
    ) {
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addFunction(new TwigFunction('path', [$this->router, 'generateUri']));
    }

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    protected function get(string $name): mixed
    {
        return $this->container->get($name);
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    protected function render(string $name, array $context = []): string
    {
        return $this->twig->render($name, $context);
    }
}
