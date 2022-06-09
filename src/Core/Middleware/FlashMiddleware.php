<?php

namespace Blog\Core\Middleware;

use Blog\Core\Service\FlashService;
use Blog\Core\Session;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FlashMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly FlashService $messages
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $request->getAttribute(Session::class);
        if ($session->get('flashMessages')) {
            $this->messages->setMessages($session->get('flashMessages'));
        }
        $response = $handler->handle($request);
        $session->set('flashMessages', $this->messages->getMessages());
        return $response;
    }
}
