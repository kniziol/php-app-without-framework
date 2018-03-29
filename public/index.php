<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use FastRoute\RouteCollector;
use Kni\HelloWorld;
use Middlewares\FastRoute;
use Middlewares\RequestHandler;
use Relay\Relay;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\SapiEmitter;
use Zend\Diactoros\ServerRequestFactory;
use function DI\create;
use function DI\get;
use function FastRoute\simpleDispatcher;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$containerBuilder = new ContainerBuilder();
$containerBuilder
    ->useAutowiring(false)
    ->useAnnotations(false)
    ->addDefinitions([
        HelloWorld::class => create(HelloWorld::class)->constructor(get('name'), get('response')),
        'name'            => 'Jane',
        'response'        => function () {
            return new Response();
        },
    ]);

try {
    $container = $containerBuilder->build();
} catch (Exception $e) {
}

$routes = simpleDispatcher(function (RouteCollector $collector) {
    $collector->get('/hello', HelloWorld::class);
});

$middlewareQueue = [
    new FastRoute($routes),
    new RequestHandler($container),
];

$requestHandler = new Relay($middlewareQueue);
$response = $requestHandler->handle(ServerRequestFactory::fromGlobals());

$emitter = new SapiEmitter();
$emitter->emit($response);
