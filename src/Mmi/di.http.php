<?php

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Http\Response;
use Mmi\Http\ResponseDebugger;
use Mmi\Mvc\Router;
use Psr\Container\ContainerInterface;

use function DI\autowire;
use function DI\get;

return [
    HttpServerEnv::class => autowire(HttpServerEnv::class),

    Request::class => function (ContainerInterface $container) {
        return (new Request())->setParams(
            $container->get(Router::class)->decodeUrl($container->get(HttpServerEnv::class)->requestUri)
        );
    },

    Response::class         => autowire(Response::class)->method('setDebug', get('app.debug.enabled')),
    ResponseDebugger::class => autowire(ResponseDebugger::class),
];
