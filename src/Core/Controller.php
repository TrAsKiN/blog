<?php

namespace Blog\Core;

use Composer\Autoload\ClassMapGenerator;
use DI\DependencyException;
use DI\NotFoundException;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;

abstract class Controller
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly Router $router,
        private readonly App $app
    ) {
        $this->twig->addExtension(new IntlExtension());
        $extensions = ClassMapGenerator::createMap(__DIR__ . '/TwigExtension');
        foreach ($extensions as $class => $file) {
            $this->twig->addExtension($this->app->getContainer()->get($class));
        }
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
