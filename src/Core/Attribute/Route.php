<?php

namespace Blog\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public string $controller;
    public string $action;
    public string $pattern;
    public array $parameters = [];

    public function __construct(
        public readonly string $path,
        public readonly string $name,
        public array $roles = [],
        public bool $restricted = false
    ) {
        $pattern = '#\{\w+}#';
        preg_match_all($pattern, $this->path, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $this->parameters[] = substr($match[0], 1, -1);
        }
        $this->pattern = preg_replace($pattern, '([0-9a-z-]+)', $this->path);
    }
}
