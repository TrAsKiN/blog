<?php

namespace Framework\Middleware;

use Exception;
use Framework\Attribute\Route;
use Framework\Authentication\UserProvider;
use Framework\Router;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SecurityMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Router $router,
        private readonly UserProvider $provider
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute(Route::class);
        if (!$route instanceof Route || !$route->restricted) {
            return $handler->handle($request);
        }
        if (!$this->provider->isAuthenticated()) {
            return new RedirectResponse($this->router->generateUri('home'));
        }
        $user = $this->provider->getUser();
        $missingRoles = array_diff($route->roles, $user->getRoles());
        if (!empty($missingRoles)) {
            return new RedirectResponse($this->router->generateUri('home'));
        }
        return $handler->handle($request);
    }
}
