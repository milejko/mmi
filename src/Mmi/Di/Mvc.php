<?php

use DI\Definition\Exception\InvalidDefinition;
use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Mvc\ActionHelper;
use Mmi\Mvc\EmptyRouterConfig;
use Mmi\Mvc\Messenger;
use Mmi\Mvc\Router;
use Mmi\Mvc\RouterConfigAbstract;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\HelperAbstract;
use Mmi\Translate;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function DI\autowire;

return [
    ActionHelper::class => autowire(ActionHelper::class),

    Messenger::class    => autowire(Messenger::class),

    Router::class       => function (ContainerInterface $container) {
        try {
            $routerConfig = $container->get(RouterConfigAbstract::class);
        } catch (InvalidDefinition $e) {
            $routerConfig = new EmptyRouterConfig();
            $container->get(LoggerInterface::class)->warning('Router config implementing ' . RouterConfigAbstract::class . ' cannot be injected. To fix this, declare class for ' . RouterConfigAbstract::class . ' in your App DI folder');
        }
        return new Router($routerConfig);
    },

    View::class => function (ContainerInterface $container) {
        return (new View($container->get(Translate::class)))->setCache($container->get('PrivateCacheService'))
            //opcja kompilacji
            ->setAlwaysCompile(!$container->get('cache.private.enabled'))
            //ustawienie cdn
            ->setCdn($container->get('app.view.cdn'))
            //ustawienie requestu
            ->setRequest($container->get(Request::class))
            //ustawianie baseUrl
            ->setBaseUrl($container->get(HttpServerEnv::class)->baseUrl);
    },
    HelperAbstract::class => autowire(HelperAbstract::class),
];
