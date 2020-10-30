<?php

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\HelperAbstract;
use Psr\Container\ContainerInterface;

use function DI\autowire;

return [
    View::class => function (ContainerInterface $container) {
        return (new View)->setCache($container->get('PrivateCacheService'))
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