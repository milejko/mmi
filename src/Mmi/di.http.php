<?php

namespace Mmi\Http;

use Mmi\Mvc\Router;
use Psr\Container\ContainerInterface;

use function DI\autowire;
use function DI\get;

return [
    Request::class => function (ContainerInterface $container) {
        $request = Request::createFromGlobals();
        //router apply (with baseUrl calculation)
        $calculatedRequestUri = substr($request->getServer()->requestUri, strlen($container->get('app.base.url')));
        return $request->setParams($container->get(Router::class)->decodeUrl($calculatedRequestUri));
    },
    ResponseDebugger::class => autowire(ResponseDebugger::class),
    Response::class => autowire(Response::class)
        ->constructorParameter('baseUrl', get('app.base.url'))
        ->method('setDebug', get('app.debug.enabled')),
];
