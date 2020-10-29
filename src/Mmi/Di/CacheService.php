<?php

use Mmi\Cache\Cache;
use Mmi\Cache\CacheConfig;
use Psr\Container\ContainerInterface;

return [
    'PrivateCacheService' => DI\factory(function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active  = $container->get('cache.private.enabled');
        $config->handler = function_exists('\apcu_fetch') ? 'apc' : 'files';
        $config->path    = BASE_PATH . '/var/cache';
        return new Cache($config);
    }),
    Cache::class => DI\factory(function (ContainerInterface $container) {
        $config = new CacheConfig();
        $config->active  = $container->get('cache.enabled');
        $config->handler = $container->get('cache.handler');
        $config->path    = $container->get('cache.path');
        return new Cache($config);
    }),
];