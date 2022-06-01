<?php

use Blog\Core\App;
use DI\ContainerBuilder;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

$rootPath = __DIR__ . '/..';
require_once $rootPath . '/vendor/autoload.php';

try {
    $builder = new ContainerBuilder();
    if (!file_exists($rootPath . '/config.php')) {
        throw new RuntimeException("The configuration file does not exist!");
    }
    $builder->addDefinitions($rootPath . '/config.php');
    $builder->addDefinitions($rootPath . '/config/definitions.php');
    $container = $builder->build();

    $app = $container->get(App::class);
    $response = $app->run(ServerRequestFactory::fromGlobals());
    (new SapiEmitter())->emit($response);
} catch (Exception $exception) {
    (new SapiEmitter())->emit(new HtmlResponse($exception->getMessage(), 500));
}
