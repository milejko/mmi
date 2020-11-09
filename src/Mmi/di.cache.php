<?php

namespace Mmi\Cache;

use Psr\Container\ContainerInterface;

use function DI\env;

return [
    'cache.private.enabled'     => env('CACHE_PRIVATE_ENABLED', false),
    'cache.public.enabled'      => env('CACHE_PUBLIC_ENABLED', false),
    'cache.public.handler'      => env('CACHE_PUBLIC_HANDLER', 'file'),
    'cache.public.path'         => env('CACHE_PUBLIC_PATH', BASE_PATH . '/var/cache'),
    'cache.public.distributed'  => env('CACHE_PUBLIC_DISTRIBUTED', false),

    SystemCacheInterface::class => function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active  = $container->get('cache.private.enabled');
        $config->handler = \function_exists('apcu_fetch') ? 'apc' : 'file';
        $config->path    = BASE_PATH . '/var/cache';
        return new Cache($config);
    },
    CacheInterface::class => function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active      = $container->get('cache.public.enabled');
        $config->handler     = $container->get('cache.public.handler');
        $config->path        = $container->get('cache.public.path');
        #for clusters shared cache must be distributed for non shared backends
        $config->distributed = $container->get('cache.public.distributed');
        return new Cache($config);
    },
];
