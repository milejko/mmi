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
        //router apply (with baseUrl calculation)
        $calculatedRequestUri = substr($request->getServer()->requestUri, strlen($container->get('app.base.url')));
        $container->get(AppProfilerInterface::class)->event(Request::class . ': request created from globals');
        $request->setParams($container->get(Router::class)->decodeUrl($calculatedRequestUri));
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
