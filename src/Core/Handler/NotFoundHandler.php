<?php

namespace Blog\Core\Handler;

use Composer\Autoload\ClassMapGenerator;
use InvalidArgumentException;
use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extra\Intl\IntlExtension;

class NotFoundHandler implements RequestHandlerInterface
{
    /**
     * @throws RuntimeException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(
        private readonly Environment $twig,
        private readonly ContainerInterface $container,
    ) {
        $this->twig->addExtension($this->container->get(IntlExtension::class));
        $extensions = ClassMapGenerator::createMap(__DIR__ . '/../TwigExtension');
        foreach ($extensions as $class => $file) {
            $this->twig->addExtension($this->container->get($class));
        }
    }

    /**
     * @throws SyntaxError
     * @throws InvalidArgumentException
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->twig->render('error/404.html.twig'));
    }
}
