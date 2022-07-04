<?php

namespace Framework;

use Composer\Autoload\ClassMapGenerator;
use Framework\Attribute\Route;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;

class Router
{
    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    /**
     * @throws ReflectionException
     * @throws RuntimeException
     */
    public function __construct()
    {
        $controllers = ClassMapGenerator::createMap(__DIR__ . '/../src/Controller');
        foreach ($controllers as $class => $file) {
            $reflection = new ReflectionClass($class);
            foreach ($reflection->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class);
                if (empty($attributes)) {
                    continue;
                }
                foreach ($attributes as $route) {
                    $this->addRoute($route->newInstance(), $class, $method->getName());
                }
            }
        }
    }

    public function match(ServerRequestInterface $request): ?array
    {
        foreach ($this->routes as $route) {
            if (preg_match(sprintf("#^%s$#", $route->pattern), $request->getUri()->getPath(), $matches)) {
                array_shift($matches);
                return [
                    'route' => $route,
                    'params' => array_combine($route->parameters, $matches),
                ];
            }
        }
        return null;
    }

    public function generateUri(string $name, array $parameters = []): ?string
    {
        $route = $this->getRoute($name);
        if ($route) {
            $uri = $route->path;
            foreach ($route->parameters as $parameter) {
                $uri = preg_replace(sprintf('#\{%s}#', $parameter), $parameters[$parameter], $uri);
            }
            return $uri;
        }
        return null;
    }

    public function generateAbsoluteUri(string $name, array $parameters = []): ?string
    {
        if ($uri = $this->generateUri($name, $parameters)) {
            return $_SERVER['SERVER_NAME'] . $uri;
        }
        return null;
    }

    private function addRoute(Route $route, string $controller, string $action): void
    {
        $route->controller = $controller;
        $route->action = $action;
        $this->routes[$route->name] = $route;
    }

    private function getRoute(string $name): ?Route
    {
        foreach ($this->routes as $route) {
            if ($route->name === $name) {
                return $route;
            }
        }
        return null;
    }
}
