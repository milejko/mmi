<?php

use Mmi\Http\HttpServerEnv;

use function DI\create;

return [
    HttpServerEnv::class => create(HttpServerEnv::class),
];