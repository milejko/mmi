<?php
declare(strict_types=1);

namespace Mmi\Mvc;

use Mmi\Http\Request;

final class RouterApply
{
    private Router $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function __invoke(Request $request): void
    {
        $request->setParams($this->router->decodeUrl($request->getServer()->requestUri));

        if (!$request->module) {
            $request
                ->setModuleName('mmi')
                ->setControllerName('index')
                ->setActionName('error');
        }
    }
}
