<?php

namespace Mmi\Cache;

use Psr\Container\ContainerInterface;

use function DI\env;

return [
    'cache.system.enabled'      => env('CACHE_SYSTEM_ENABLED', false),
    'cache.public.enabled'      => env('CACHE_PUBLIC_ENABLED', false),
    'cache.public.handler'      => env('CACHE_PUBLIC_HANDLER', 'file'),
    'cache.public.path'         => env('CACHE_PUBLIC_PATH', BASE_PATH . '/var/cache'),
    'cache.public.lifetime'     => env('CACHE_PUBLIC_LIFETIME', 300),

    SystemCacheInterface::class => function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active     = $container->get('cache.system.enabled');
        $config->handler    = \function_exists('apcu_fetch') ? 'apc' : 'file';
        $config->path       = BASE_PATH . '/var/cache';
        $config->lifetime   = 2592000;
        return new Cache($config);
    },
    CacheInterface::class => function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active      = $container->get('cache.public.enabled');
        $config->handler     = $container->get('cache.public.handler');
        $config->path        = $container->get('cache.public.path');
        $config->lifetime    = $container->get('cache.public.lifetime');
        return new Cache($config);
    },
];
