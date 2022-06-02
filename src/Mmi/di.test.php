<?php

namespace Mmi\Mvc;

use Mmi\TestKit\TestRouterConfig;
use function DI\create;

return [
    RouterConfig::class => create(TestRouterConfig::class),
];