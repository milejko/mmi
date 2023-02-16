<?php

namespace Mmi\App;

use function DI\autowire;
use function DI\env;

return [
    'app.debug.enabled' => env('APP_DEBUG_ENABLED', true),
    'app.view.cdn'      => env('APP_VIEW_CDN', ''),
    'app.compile.path'  => env('APP_COMPILE_PATH', BASE_PATH . '/var/cache'),
    'app.time.zone'     => env('APP_TIME_ZONE', 'Europe/Warsaw'),

    AppExceptionFormatterInterface::class   => autowire(AppExceptionFormatter::class),
    AppExceptionLoggerInterface::class      => autowire(AppExceptionLogger::class),
    AppErrorHandlerInterface::class         => autowire(AppErrorHandler::class),
];
