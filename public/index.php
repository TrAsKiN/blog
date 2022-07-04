<?php

use DI\ContainerBuilder;
use Framework\App;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Twig\Environment;

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
    $response = $app->run();
    (new SapiEmitter())->emit($response);
} catch (Exception $exception) {
    (new SapiEmitter())->emit(new HtmlResponse(
        ($container->get(Environment::class))->render('error/500.html.twig', compact('exception')),
        500
    ));
}
