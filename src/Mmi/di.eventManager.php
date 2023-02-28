<?php

namespace Mmi\App;

use Mmi\EventManager\EventManager;
use Mmi\EventManager\EventManagerInterface;

use function DI\autowire;

return [
    EventManagerInterface::class   => autowire(EventManager::class),
];
