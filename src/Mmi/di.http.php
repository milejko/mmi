<?php

namespace Mmi\Http;

use function DI\autowire;
use function DI\get;

return [
    ResponseDebugger::class => autowire(ResponseDebugger::class),
    Response::class => autowire(Response::class)
        ->constructorParameter('baseUrl', get('app.base.url'))
        ->method('setDebug', get('app.debug.enabled')),
];
