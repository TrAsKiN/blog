<?php

namespace Blog;

use DI\Container;
use Exception;
use Laminas\Diactoros\Response\EmptyResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class App
{
    public function __construct(
        private readonly Container $container,
        private readonly Router $router
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws Exception
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        $matches = $this->router->match($request);
        if (!empty($matches)) {
            $response = $this->container->call([
                $matches['route']->controller,
                $matches['route']->action
            ], $matches['params']);
            if (!$response instanceof ResponseInterface) {
                throw new Exception("The Controller has not returned a response implementing ResponseInterface!");
            }
            return $response;
        } else {
            return new EmptyResponse(404);
        }
    }
}
