<?php

namespace Mmi\EventManager;

interface EventInterface
{
    public function getName(): string;

    public function getTarget();

    public function getParams(): array;

    public function getParam(string $name, mixed $default = null);

    public function setName(string $name): void;

    public function setTarget(mixed $target): void;

    public function setParams(array $params): void;

    public function setParam(string $name, mixed $value): void;

    public function stopPropagation(bool $flag = true);

    public function propagationIsStopped(): bool;
}
