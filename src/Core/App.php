<?php

namespace Blog\Core;

use Blog\Core\Handler\NotFoundHandler;
use Blog\Core\Handler\RequestHandler;
use Blog\Core\Middleware\AuthenticationMiddleware;
use Blog\Core\Middleware\ControllerMiddleware;
use Blog\Core\Middleware\FlashMiddleware;
use Blog\Core\Middleware\RoutingMiddleware;
use Blog\Core\Middleware\SecurityMiddleware;
use Blog\Core\Middleware\SessionMiddleware;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class App
{
    private RequestHandlerInterface $handler;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct(
        private readonly Container $container,
        private readonly ServerRequestInterface $request
    ) {
        $this->handler = new RequestHandler($this->container->get(NotFoundHandler::class));
        $this->handler->pipe([
            $this->container->get(RoutingMiddleware::class),
            $this->container->get(SessionMiddleware::class),
            $this->container->get(FlashMiddleware::class),
            $this->container->get(AuthenticationMiddleware::class),
            $this->container->get(SecurityMiddleware::class),
            $this->container->get(ControllerMiddleware::class),
        ]);
    }

    public function run(): ResponseInterface
    {
        return $this->handler->handle($this->request);
    }
}
