<?php

use Mmi\App\AppErrorHandler;

use function DI\autowire;
use function DI\env;

return [
    'app.debug.enabled' => env('APP_DEBUG_ENABLED', true),
    'app.view.cdn'      => env('APP_VIEW_CDN'),

    AppErrorHandler::class => autowire(AppErrorHandler::class),
];
