<?php

namespace Mmi\EventManager;

use SplStack;

class ResponseCollection extends SplStack
{
    protected bool $stopped = false;

    public function stopped(): bool
    {
        return $this->stopped;
    }

    public function setStopped($flag): ResponseCollection
    {
        $this->stopped = (bool)$flag;
        return $this;
    }

    public function first(): mixed
    {
        return parent::bottom();
    }

    public function last(): mixed
    {
        if (0 !== count($this)) {
            return parent::top();
        }
    }

    public function contains($value): bool
    {
        foreach ($this as $response) {
            if ($response === $value) {
                return true;
            }
        }
        return false;
    }
}
