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
        $router = $this->container->get(Router::class);
        $matches = $router->match($request);
        if (!empty($matches)) {
            $response = $this->container->call([
                $matches['route']->controller,
                $matches['route']->action
            ], $matches['params']);
            return new HtmlResponse($response);
        } else {
            return new EmptyResponse(404);
        }
    }
}
