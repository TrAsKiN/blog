<?php

namespace Blog\Core\Middleware;

use Blog\Core\Attribute\Route;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ControllerMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        $params = $request->getAttribute('params');
        $params['request'] = $request;
        if (!$route instanceof Route) {
            return $handler->handle($request);
        }
        return $this->container->call([
            $route->controller,
            $route->action
        ], $params);
    }
}
