<?php

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Mvc\Router;
use Psr\Container\ContainerInterface;

use function DI\create;

return [
    Request::class => function (ContainerInterface $container) {
        return (new Request())
            ->setParams(
                $container->get(Router::class)->decodeUrl(
                    $container->get(HttpServerEnv::class)->requestUri
                ));
    }
];