<?php

namespace Framework;

use Blog\Entity\User;
use Framework\Authentication\UserProvider;
use InvalidArgumentException;
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

abstract class Controller
{
    public function __construct(
        protected readonly Environment $twig,
        private readonly Router $router,
        private readonly ContainerInterface $container,
        private readonly UserProvider $provider
    ) {
    }

    /**
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws InvalidArgumentException
     */
    protected function render(string $name, array $context = []): ResponseInterface
    {
        return new HtmlResponse($this->twig->render($name, $context));
    }

    /**
     * @throws InvalidArgumentException
     */
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

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function get(string $id)
    {
        return $this->container->get($id);
    }
}
