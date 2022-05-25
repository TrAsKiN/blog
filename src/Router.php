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
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        foreach ($this->routes as $route) {
            if ($request->getUri()->getPath() == $route->path) {
                return $route;
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
