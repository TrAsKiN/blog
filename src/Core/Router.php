<?php

namespace Blog\Core;

use Blog\Core\Attribute\Route;
use Composer\Autoload\ClassMapGenerator;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;
use ReflectionException;

class Router
{
    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        $controllers = ClassMapGenerator::createMap(__DIR__ . '/../Controller');
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

    /**
     * @param ServerRequestInterface $request
     * @return array|null
     */
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

    /**
     * @param string $name
     * @param array $parameters
     * @return string|null
     */
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

    /**
     * @param Route $route
     * @param $controller
     * @param $action
     */
    private function addRoute(Route $route, $controller, $action): void
    {
        $route->controller = $controller;
        $route->action = $action;
        $this->routes[$route->name] = $route;
    }

    /**
     * @param string $name
     * @return Route|null
     */
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
