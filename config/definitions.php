<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\create;
use function DI\get;

return [
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        return new Environment($loader);
    },
    PDO::class => create(PDO::class)->constructor(
        sprintf('mysql:dbname=%s;host=%s', get('db.name'), get('db.host')),
        get('db.username'),
        get('db.password'),
        [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ]
    ),
];
