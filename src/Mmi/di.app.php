<?php

namespace Mmi\App;

use function DI\autowire;
use function DI\env;

return [
    'app.debug.enabled' => env('APP_DEBUG_ENABLED', true),
    'app.view.cdn'      => env('APP_VIEW_CDN', ''),

    AppExceptionFormatter::class    => autowire(AppExceptionFormatter::class),
    AppExceptionLogger::class       => autowire(AppExceptionLogger::class),
    AppErrorHandler::class          => autowire(AppErrorHandler::class),
];
