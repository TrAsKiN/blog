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
        if (!$session->get('username')) {
            return $handler->handle($request);
        }
        $user = $this->provider->retrieve($session->get('username'));
        if (!$user instanceof User) {
            throw new Exception(
                sprintf("Username '%s' does not exists!", $this->provider->getUser())
            );
        }
        if ($user->getToken() === $session->get('token')) {
            $this->provider->setAuthenticated(true);
        }
        return $handler->handle($request);
    }
}
