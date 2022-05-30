<?php

namespace Blog\Core;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public string $controller;
    public string $action;
    public array $parameters = [];
    public string $pattern;

    public function __construct(
        public readonly string $path,
        public readonly string $name
    ) {
        $pattern = '#\{\w+}#';
        preg_match_all($pattern, $this->path, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $this->parameters[] = substr($match[0], 1, -1);
        }
        $this->pattern = preg_replace($pattern, '(\d+)', $this->path);
    }
}
