<?php

use Mmi\App\KernelEventHandler;

return [
    KernelEventHandler::class   => DI\autowire(KernelEventHandler::class)
];