<?php

namespace Mmi\Mvc;

use Mmi\Mvc\ViewHelper\HelperAbstract;

use function DI\autowire;
use function DI\get;

return [
    ActionHelper::class => autowire(ActionHelper::class),

    Messenger::class => autowire(Messenger::class),

    Router::class => autowire(Router::class),

    View::class => autowire(View::class)
        ->method('setCdn', get('app.view.cdn')),

    HelperAbstract::class => autowire(HelperAbstract::class),
];
