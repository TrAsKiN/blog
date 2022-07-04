<?php

namespace Framework;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use Framework\Handler\NotFoundHandler;
use Framework\Handler\RequestHandler;
use Framework\Middleware\AuthenticationMiddleware;
use Framework\Middleware\ControllerMiddleware;
use Framework\Middleware\FlashMiddleware;
use Framework\Middleware\RoutingMiddleware;
use Framework\Middleware\SecurityMiddleware;
use Framework\Middleware\SessionMiddleware;
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
