<?php

use Laminas\Diactoros\ServerRequestFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    ServerRequestInterface::class => function () {
        return ServerRequestFactory::fromGlobals();
    },
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        return new Environment($loader);
    },
    PDO::class => function (ContainerInterface $container) {
        return new PDO(
            sprintf('mysql:dbname=%s;host=%s', $container->get('db.name'), $container->get('db.host')),
            $container->get('db.username'),
            $container->get('db.password'),
            [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_CLASS,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]
        );
    },
    Mailer::class => function (ContainerInterface $container) {
        return new Mailer(Transport::fromDsn($container->get('mailer')));
    },
];
