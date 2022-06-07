<?php

namespace Blog\Core\Middleware;

use Blog\Core\Attribute\Route;
use Blog\Core\Router;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Router $router
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matches = $this->router->match($request);
        if (empty($matches)) {
            return $handler->handle($request);
        }
        return $handler->handle(
            $request
                ->withAttribute(Route::class, $matches['route'])
                ->withAttribute('params', $matches['params'])
        );
    }
}
