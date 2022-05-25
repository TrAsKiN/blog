<?php

namespace Blog;

use DI\Container;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laminas\Diactoros\Response\HtmlResponse;

class App
{
    public function __construct(
        public Container $container
    ) {
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $route = $this->container->get(Router::class)->match($request);
        if ($route) {
            return new HtmlResponse($this->container->call([
                $route->controller,
                $route->action
            ]));
        } else {
            return new EmptyResponse(404);
        }
    }
}
