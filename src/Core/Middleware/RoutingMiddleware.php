<?php

namespace Blog\Core\Middleware;

use Blog\Core\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly ContainerInterface $container
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matches = $this->router->match($request);
        if (!empty($matches)) {
            return $this->container->call([
                $matches['route']->controller,
                $matches['route']->action
            ], $matches['params']);
        }
        return $handler->handle($request);
    }
}
