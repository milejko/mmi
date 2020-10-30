<?php

use Mmi\Cache\Cache;
use Mmi\Cache\CacheConfig;
use Psr\Container\ContainerInterface;

use function DI\env;
use function DI\get;

return [
    'cache.private.enabled'     => env('CACHE_PRIVATE_ENABLED'),
    'cache.public.enabled'      => env('CACHE_PUBLIC_ENABLED', false),
    'cache.public.handler'      => env('CACHE_PUBLIC_HANDLER', 'file'),
    'cache.public.path'         => env('CACHE_PUBLIC_PATH', BASE_PATH . '/var/cache'),
    'cache.public.distributed'  => env('CACHE_PUBLIC_DISTRIBUTED', false),

    'PrivateCacheService' => function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active  = $container->get('cache.private.enabled');
        $config->handler = function_exists('\apcu_fetch') ? 'apc' : 'file';
        $config->path    = BASE_PATH . '/var/cache';
        return new Cache($config);
    },
    Cache::class => function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active      = $container->get('cache.public.enabled');
        $config->handler     = $container->get('cache.public.handler');
        $config->path        = $container->get('cache.public.path');
        $config->distributed = $container->get('cache.public.distributed');
        return new Cache($config);
    },
    'PublicCacheService' => get(Cache::class),
];
