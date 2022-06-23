<?php

use Composer\Autoload\ClassMapGenerator;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\FilesystemLoader;

return [
    Environment::class => function (ContainerInterface $container) {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader);
        $twig->addExtension($container->get(IntlExtension::class));
        $extensions = ClassMapGenerator::createMap(__DIR__ . '/../src/Core/TwigExtension');
        foreach ($extensions as $class => $file) {
            $twig->addExtension($container->get($class));
        }
        return $twig;
    },
    Mailer::class => function (ContainerInterface $container) {
        return new Mailer(Transport::fromDsn($container->get('mailer')));
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
    ServerRequestInterface::class => function () {
        return ServerRequestFactory::fromGlobals();
    },
];
