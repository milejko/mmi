<?php

use Mmi\Mvc\ActionHelper;

use function DI\autowire;

return [
    ActionHelper::class => autowire(ActionHelper::class),
];