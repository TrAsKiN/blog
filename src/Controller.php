<?php

namespace Blog;

use Twig\Environment;

class Controller
{
    public function __construct(
        protected Environment $twig
    ) {
    }
}
