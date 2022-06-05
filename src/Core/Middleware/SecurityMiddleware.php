<?php

namespace Blog\Core\Middleware;

use Blog\Core\Session;
use Blog\Core\UserProvider;
use Blog\Entity\User;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SecurityMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Session $session,
        private readonly UserProvider $provider
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->session->get('username')) {
            return $handler->handle($request);
        }
        $user = $this->provider->retrieve($this->session->get('username'));
        if (!$user instanceof User) {
            throw new Exception(
                sprintf("Username '%s' does not exists!", $this->provider->getUser()->getUsername())
            );
        }
        $this->session->set('username', $user->getUsername());
        return $handler->handle($request);
    }

    private function getOptimalCost(): int
    {
        $timeTarget = 0.05;
        $cost = 8;
        do {
            $cost++;
            $start = microtime(true);
            password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);
        return $cost;
    }
}
