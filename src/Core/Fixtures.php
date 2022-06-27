<?php

namespace Blog\Core;

use Faker\Factory;
use Faker\Generator;

abstract class Fixtures
{
    use SlugTrait;

    protected readonly Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
}
