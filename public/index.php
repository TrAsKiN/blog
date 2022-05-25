<?php

use Blog\App;
use DI\ContainerBuilder;
use Laminas\Diactoros\ServerRequestFactory;

use function Http\Response\send;

$rootPath = __DIR__ . '/..';
require_once $rootPath . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions($rootPath . '/config/definitions.php');
$container = $builder->build();

$app = $container->get(App::class);
$response = $app->run(ServerRequestFactory::fromGlobals());
send($response);
