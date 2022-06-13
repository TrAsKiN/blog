<?php

namespace Blog\Core\Middleware;

use Blog\Core\Authentication\UserProvider;
use Blog\Core\Session;
use Blog\Entity\User;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly UserProvider $provider
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute(Session::class);
        if (!$session->get('token')) {
            return $handler->handle($request);
        }
        $user = $this->provider->retrieve($session->get('token'));
        if (!$user instanceof User) {
            throw new Exception(
                sprintf("No user match with token '%s'!", $session->get('token'))
            );
        }
        $this->provider->setAuthenticated(true);
        return $handler->handle($request);
    }
}
