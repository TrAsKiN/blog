<?php

use Blog\App;
use DI\ContainerBuilder;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

$rootPath = __DIR__ . '/..';
require_once $rootPath . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions($rootPath . '/config/definitions.php');
$container = $builder->build();

$app = $container->get(App::class);
$response = $app->run(ServerRequestFactory::fromGlobals());
(new SapiEmitter())->emit($response);
