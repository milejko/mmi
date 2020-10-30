<?php

use Mmi\App\AppEventHandler;

use function DI\autowire;
use function DI\env;

return [
    'app.debug.enabled' => env('APP_DEBUG_ENABLED', true),
    'app.view.cdn'      => env('APP_VIEW_CDN'),

    AppEventHandler::class => autowire(AppEventHandler::class),
];
