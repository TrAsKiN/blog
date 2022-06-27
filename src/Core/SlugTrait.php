<?php

namespace Blog\Core;

trait SlugTrait
{
    public static function slugify(string $string): string
    {
        return strtolower(trim(preg_replace('/[^\w-]+/', '-', $string)));
    }
}
