<?php

namespace Mmi\EventManager;

class Event implements EventInterface
{
    protected string $name;

    protected object|null $target;

    protected array $params = [];

    protected bool $stopPropagation = false;

    public function __construct(string $name = null, ?object $target = null, ?array $params = null)
    {
        if (null !== $name) {
            $this->setName($name);
        }

        if (null !== $target) {
            $this->setTarget($target);
        }

        if (null !== $params) {
            $this->setParams($params);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTarget(): mixed
    {
        return $this->target;
    }

    public function setParams(array $params): void
    {
        if (!is_array($params)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Event parameters must be an array; received "%s"', gettype($params))
            );
        }

        $this->params = $params;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam(string $name, mixed $default = null)
    {
        if (is_array($this->params)) {
            if (!isset($this->params[$name])) {
                return $default;
            }

            return $this->params[$name];
        }
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setTarget(mixed $target): void
    {
        $this->target = $target;
    }

    public function setParam(string $name, mixed $value): void
    {
        $this->params[$name] = $value;
    }

    public function stopPropagation(bool $flag = true): void
    {
        $this->stopPropagation = (bool)$flag;
    }

    public function propagationIsStopped(): bool
    {
        return $this->stopPropagation;
    }
}
