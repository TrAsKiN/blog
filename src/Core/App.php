<?php

namespace Blog\Core;

use Blog\Core\Handler\NotFoundHandler;
use Blog\Core\Handler\RequestHandler;
use Blog\Core\Middleware\RoutingMiddleware;
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
        private readonly Container $container
    ) {
        $this->handler = new RequestHandler(new NotFoundHandler());
        $this->handler->add($this->container->get(RoutingMiddleware::class));
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handler->handle($request);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}
