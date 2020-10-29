<?php

use Monolog\Logger;
use function DI\env;

return [
    'app.debug.enabled'         => env('APP_DEBUG_ENABLED', true),
    'app.view.cdn'              => env('APP_VIEW_CDN'),

    'log.level'                 => env('LOG_LEVEL', Logger::DEBUG),
    'log.handler'               => env('LOG_HANDLER', ''),
    'log.path'                  => env('LOG_PATH'),
    
    'cache.private.enabled'     => env('CACHE_PRIVATE_ENABLED'),
    'cache.enabled'             => env('CACHE_ENABLED', false),
    'cache.handler'             => env('CACHE_HANDLER', 'files'),
    'cache.path'                => env('CACHE_PATH', BASE_PATH . '/var/cache'),
];