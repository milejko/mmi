<?php

use Mmi\App\AppEventHandler;

return [
    AppEventHandler::class => DI\autowire(AppEventHandler::class)
];