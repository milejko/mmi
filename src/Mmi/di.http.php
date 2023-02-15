<?php

namespace Mmi\Http;

use Mmi\App\AppProfilerInterface;
use Mmi\Mvc\Router;
use Psr\Container\ContainerInterface;

use function DI\autowire;
use function DI\get;

return [
    Request::class => function (ContainerInterface $container) {
        $request = Request::createFromGlobals();
        $container->get(AppProfilerInterface::class)->event(Request::class . ': request created from globals');
        $request->setParams($container->get(Router::class)->decodeUrl($request->getServer()->requestUri));
        //url decoded to 404
        if (!$request->module) {
            $request
                ->setModuleName('mmi')
                ->setControllerName('index')
                ->setActionName('error');
        }
        return $request;
    },
    ResponseDebugger::class => autowire(ResponseDebugger::class),
    Response::class => autowire(Response::class)
        ->method('setDebug', get('app.debug.enabled')),
];
