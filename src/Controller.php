<?php

namespace Blog;

use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;

class Controller
{
    public function __construct(
        protected Environment $twig
    ) {
        $this->twig->addExtension(new IntlExtension());
    }
}
