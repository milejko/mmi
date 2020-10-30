<?php

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Mvc\ActionHelper;
use Mmi\Mvc\Router;
use Mmi\Mvc\RouterConfig;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\HelperAbstract;
use Mmi\Translate;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return [
    ActionHelper::class => autowire(ActionHelper::class),

    RouterConfig::class => autowire(\App\RouterConfig::class),
    Router::class       => autowire(Router::class),

    View::class => function (ContainerInterface $container) {
        return (new View($container->get(Translate::class)))->setCache($container->get('PrivateCacheService'))
            //opcja kompilacji
            ->setAlwaysCompile(!$container->get('cache.private.enabled'))
            //ustawienie cdn
            ->setCdn($container->get('app.view.cdn'))
            //ustawienie requestu
            ->setRequest($container->get(Request::class))
            //ustawianie baseUrl
            ->setBaseUrl($container->get(HttpServerEnv::class));
    },
    HelperAbstract::class => autowire(HelperAbstract::class),
];
