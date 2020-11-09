<?php

namespace Mmi\App;

use function DI\autowire;
use function DI\env;

return [
    'app.debug.enabled' => env('APP_DEBUG_ENABLED', true),
    'app.view.cdn'      => env('APP_VIEW_CDN', ''),

    AppExceptionFormatterInterface::class   => autowire(AppExceptionFormatter::class),
    AppExceptionLoggerInterface::class      => autowire(AppExceptionLogger::class),
    AppErrorHandlerInterface::class         => autowire(AppErrorHandler::class),
];
