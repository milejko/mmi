<?php
declare(strict_types=1);

namespace Mmi\TestKit;

use Mmi\Mvc\RouterConfig;

final class TestRouterConfig extends RouterConfig
{
    public function __construct()
    {
        $this->setRoute(
            'default',
            '',
            ['module' => 'mmi', 'controller' => 'index', 'action' => 'index', 'uri' => '/']
        );
    }
}
