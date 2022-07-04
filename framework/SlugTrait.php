<?php

namespace Framework;

trait SlugTrait
{
    public static function slugify(string $string): string
    {
        return strtolower(trim(preg_replace('/[^\w-]+/', '-', $string)));
    }
}
