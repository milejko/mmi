<?php
declare(strict_types=1);

namespace Mmi\Tests\Cache;

use Mmi\Cache\CacheInterface;

final class ObservableCacheStub implements CacheInterface
{
    private $store = [];
    private $eventLog = [];

    private const LOAD_LABEL = 'load';
    private const SAVE_LABEL = 'save';
    private const REMOVE_LABEL = 'remove';
    private const FLUSH_LABEL = 'flush';

    public function load(string $key)
    {
        $this->eventLog[] = self::LOAD_LABEL;
        return isset($this->store[$key]) ? $this->store[$key] : null;
    }

    public function save($data, string $key, ?int $lifetime = null): bool
    {
        $this->eventLog[] = self::SAVE_LABEL;
        $this->store[$key] = $data;
        return true;
    }

    public function remove(string $key): bool
    {
        $this->eventLog[] = self::REMOVE_LABEL;
        unset($this->store[$key]);
        return true;
    }

    public function flush(): void
    {
        $this->eventLog[] = self::FLUSH_LABEL;
        $this->store = [];
    }

    public function isActive(): bool
    {
        return true;
    }

    public function getEventLog(): array
    {
        return $this->eventLog;
    }

    public function flushEventLog(): void
    {
        $this->eventLog = [];
    }

}