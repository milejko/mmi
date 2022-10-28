<?php

namespace Mmi\EventManager;

interface EventManagerInterface
{
    public function trigger(string $event, mixed $target = null, array $argv = [], object $callback = null);

    public function attach(string $event, object $callback = null, int $priority = 1): object;

    public function detach(mixed $listener, string $eventName, bool $force): void;

    public function getEvents(): array;

    public function getListeners(string $event): ?array;

    public function clearListeners(string $event): void;

    public function getIdentifiers(): array;

    public function setIdentifiers(array $identifiers): void;

    public function addIdentifiers(array $identifiers): void;
}
