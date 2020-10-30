<?php

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Http\Response;
use Mmi\Http\ResponseDebugger;
use Mmi\Mvc\Router;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return [
    HttpServerEnv::class => autowire(HttpServerEnv::class),

    Request::class => function (ContainerInterface $container) {
        return (new Request())->setParams(
            $container->get(Router::class)->decodeUrl($container->get(HttpServerEnv::class)->requestUri)
        );
    },

    Response::class         => function (ContainerInterface $container) {
        return (new Response($container->get(Router::class), $container->get(HttpServerEnv::class)))
            ->setDebug($container->get('app.debug.enabled'));
    },
    ResponseDebugger::class => autowire(ResponseDebugger::class),
];
