<?php

namespace Blog\Core\Middleware;

use Blog\Core\Session;
use DI\Container;
use Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $cookies = $request->getCookieParams();
        $id = $cookies[session_name()] ?? bin2hex(random_bytes(16));
        $session = new Session($id);
        $this->container->set(Session::class, $session);
        $response = $handler->handle($request->withAttribute(Session::class, $session));
        $session->persist();
        if (empty($_SESSION)) {
            return $response;
        }
        return $response->withHeader(
            'Set-Cookie',
            sprintf(
                '%s=%s; path=%s',
                session_name(),
                $session->id,
                ini_get('session.cookie_path')
            )
        );
    }
}
