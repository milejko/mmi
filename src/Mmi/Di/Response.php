<?php

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Response;
use Mmi\Http\ResponseDebugger;
use Mmi\Mvc\Router;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return [
    ResponseDebugger::class => autowire(ResponseDebugger::class),
    Response::class         => function (ContainerInterface $container) {
        return (new Response($container->get(Router::class), $container->get(HttpServerEnv::class)))
            ->setDebug($container->get('app.debug.enabled'));
    }
];