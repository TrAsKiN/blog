<?php

namespace Blog\Core;

use Blog\Entity\User;
use Composer\Autoload\ClassMapGenerator;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;

abstract class Controller
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly Router $router,
        private readonly ContainerInterface $container,
        private readonly UserProvider $provider
    ) {
        $this->twig->addExtension(new IntlExtension());
        $extensions = ClassMapGenerator::createMap(__DIR__ . '/TwigExtension');
        foreach ($extensions as $class => $file) {
            $this->twig->addExtension($this->container->get($class));
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

    protected function getUser(): User
    {
        return $this->provider->getUser();
    }

    protected function isAuthenticated(): bool
    {
        return $this->provider->isAuthenticated();
    }
}
