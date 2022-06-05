<?php

use Blog\Core\App;
use DI\ContainerBuilder;
use Laminas\Diactoros\Response\TextResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;

$rootPath = dirname(__DIR__);
require_once $rootPath . '/vendor/autoload.php';

try {
    $builder = new ContainerBuilder();
    if (!file_exists($rootPath . '/config.php')) {
        throw new Exception("The configuration file does not exist!");
    }
    $builder->addDefinitions($rootPath . '/config.php');
    $builder->addDefinitions($rootPath . '/config/definitions.php');
    $container = $builder->build();

    $app = $container->get(App::class);
    $response = $app->run(ServerRequestFactory::fromGlobals());
    (new SapiEmitter())->emit($response);
} catch (Exception $exception) {
    (new SapiEmitter())->emit(new TextResponse(
        $exception->getMessage() . PHP_EOL.PHP_EOL . $exception->getTraceAsString(),
        500
    ));
}
