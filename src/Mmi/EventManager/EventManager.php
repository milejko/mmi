<?php

namespace Mmi\EventManager;

use Mmi\EventManager\Exception\RuntimeException;

class EventManager implements EventManagerInterface
{
    protected array $events = [];

    protected EventInterface $eventPrototype;

    protected array $identifiers = [];

    public function __construct()
    {
        $this->eventPrototype = new Event();
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    public function setIdentifiers(array $identifiers): void
    {
        $this->identifiers = array_unique($identifiers);
    }

    public function addIdentifiers(array $identifiers): void
    {
        $this->identifiers = array_unique(array_merge(
            $this->identifiers,
            $identifiers
        ));
    }

    public function trigger(string $eventName, mixed $target = null, array $argv = [], object $callback = null): ResponseCollection
    {
        $event = clone $this->eventPrototype;
        $event->setName($eventName);

        if ($target !== null) {
            $event->setTarget($target);
        }

        if ($argv) {
            $event->setParams($argv);
        }

        return $this->triggerListeners($event);
    }

    public function attach(string $eventName, object $listener = null, int $priority = 1): object
    {
        if (!is_string($eventName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string for the event; received %s',
                __METHOD__,
                is_object($eventName) ? get_class($eventName) : gettype($eventName)
            ));
        }

        $this->events[$eventName][(int)$priority][0][] = $listener;
        return $listener;
    }

    public function detach(mixed $listener, string $eventName = null, bool $force = false): void
    {
        if (null === $eventName || ('*' === $eventName && !$force)) {
            foreach (array_keys($this->events) as $eventName) {
                $this->detach($listener, $eventName, true);
            }
            return;
        }

        if (!is_string($eventName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string for the event; received %s',
                __METHOD__,
                is_object($eventName) ? get_class($eventName) : gettype($eventName)
            ));
        }

        if (!isset($this->events[$eventName])) {
            return;
        }

        foreach ($this->events[$eventName] as $priority => $listeners) {
            foreach ($listeners[0] as $index => $evaluatedListener) {
                if ($evaluatedListener !== $listener) {
                    continue;
                }

                unset($this->events[$eventName][$priority][0][$index]);

                if (empty($this->events[$eventName][$priority][0])) {
                    unset($this->events[$eventName][$priority]);
                    break;
                }
            }
        }

        if (!empty($this->events[$eventName])) {
            unset($this->events[$eventName]);
        }
    }

    public function clearListeners(string $eventName): void
    {
        if (isset($this->events[$eventName])) {
            unset($this->events[$eventName]);
        }
    }

    protected function triggerListeners(EventInterface $event, ?callable $callback = null): ResponseCollection
    {
        $name = $event->getName();

        if (empty($name)) {
            throw new RuntimeException('Event is missing a name; cannot trigger!');
        }

        if (isset($this->events[$name])) {
            $listOfListenersByPriority = $this->events[$name];

            if (isset($this->events['*'])) {
                foreach ($this->events['*'] as $priority => $listOfListeners) {
                    $listOfListenersByPriority[$priority][] = $listOfListeners[0];
                }
            }
        } elseif (isset($this->events['*'])) {
            $listOfListenersByPriority = $this->events['*'];
        } else {
            $listOfListenersByPriority = [];
        }

        krsort($listOfListenersByPriority);

        $event->stopPropagation(false);

        $responses = new ResponseCollection();
        foreach ($listOfListenersByPriority as $listOfListeners) {
            foreach ($listOfListeners as $listeners) {
                foreach ($listeners as $listener) {
                    $response = $listener($event);
                    $responses->push($response);

                    if ($event->propagationIsStopped()) {
                        $responses->setStopped(true);
                        return $responses;
                    }

                    if ($callback && $callback($response)) {
                        $responses->setStopped(true);
                        return $responses;
                    }
                }
            }
        }

        return $responses;
    }

    public function getListeners(string $event): ?array
    {
        return $this->events[$event] ?? null;
    }

    public function getEvents(): array
    {
        return array_keys($this->events);
    }
}
