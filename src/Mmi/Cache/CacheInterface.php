<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Cache;

/**
 * Cache (aka Public Cache) interface
 */
interface CacheInterface
{
    /**
     * Loads from cache
     * @return mixed
     */
    public function load(string $key);

    /**
     * Saves to cache
     * @param mixed $data dane
     */
    public function save($data, string $key, int $lifetime = null): bool;

    /**
     * Deletes from cache
     * @return boolean
     */
    public function remove(string $key): bool;

    /**
     * Flushes all buffers
     */
    public function flush(): void;

    /**
     * Is buffer active
     */
    public function isActive(): bool;
}
