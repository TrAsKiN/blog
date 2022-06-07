<?php

namespace Blog\Core\Handler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestHandler implements RequestHandlerInterface
{
    private array $middlewares = [];

    public function __construct(
        private readonly RequestHandlerInterface $fallbackHandler
    ) {
    }

    public function pipe(MiddlewareInterface|array $middlewares)
    {
        if (is_array($middlewares)) {
            foreach ($middlewares as $middleware) {
                $this->middlewares[] = $middleware;
            }
        } else {
            $this->middlewares[] = $middlewares;
        }
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (empty($this->middlewares)) {
            return $this->fallbackHandler->handle($request);
        }

        $middleware = array_shift($this->middlewares);
        return $middleware->process($request, $this);
    }
}
