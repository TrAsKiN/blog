<?php

namespace Blog;

use Composer\Autoload\ClassMapGenerator;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionClass;

class Router
{
    /**
     * @var Route[] $routes
     */
    private array $routes = [];

    public function __construct()
    {
        $controllers = ClassMapGenerator::createMap(__DIR__ . '/Controller');
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
                    'params' => $matches,
                ];
            }
        }
        return null;
    }

    /**
     * @param Route $route
     * @param $controller
     * @param $action
     * @return self
     */
    public function addRoute(Route $route, $controller, $action): self
    {
        $route->controller = $controller;
        $route->action = $action;
        $this->routes[$route->name] = $route;
        return $this;
    }
}
