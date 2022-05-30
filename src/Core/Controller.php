<?php

namespace Blog\Core;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;
use Twig\TwigFunction;

abstract class Controller
{
    public function __construct(
        private readonly Environment $twig,
        private readonly Router $router
    ) {
        $this->twig->addExtension(new IntlExtension());
        $this->twig->addFunction(new TwigFunction('path', [$this->router, 'generateUri']));
    }

    /**
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoaderError
     */
    protected function render(string $name, array $context = []): ResponseInterface
    {
        return new HtmlResponse($this->twig->render($name, $context));
    }

    protected function redirect(string $name, array $parameters = []): ResponseInterface
    {
        return new RedirectResponse($this->router->generateUri($name, $parameters));
    }
}
