<?php

namespace Blog;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Route
{
    public string $controller;
    public string $action;

    public function __construct(
        public readonly string $path,
        public readonly string $name
    ) {
    }
}
