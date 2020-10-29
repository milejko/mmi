<?php

use Mmi\Mvc\Router;
use Mmi\Mvc\RouterConfig;

use function DI\autowire;

return [
    RouterConfig::class => autowire(\App\RouterConfig::class),
    Router::class       => autowire(Router::class),
];