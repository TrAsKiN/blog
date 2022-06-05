<?php

namespace Blog\Core\Middleware;

use Blog\Core\Session;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Session $session
    ) {
    }

    /**
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $cookies = $request->getCookieParams();
        $id = $cookies[session_name()] ?? bin2hex(random_bytes(16));
        $this->session->open($id);
        $response = $handler->handle($request);
        $this->session->persist();
        if (empty($_SESSION)) {
            return $response;
        }
        return $response->withHeader(
            'Set-Cookie',
            sprintf(
                '%s=%s; path=%s',
                session_name(),
                $this->session->getId(),
                ini_get('session.cookie_path')
            )
        );
    }
}
