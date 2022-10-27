<?php
declare(strict_types=1);

namespace Mmi\Mvc;

use DI\Annotation\Inject;
use Mmi\Http\Request;

final class RouterApply
{
    private string $baseUrl;
    private Router $router;

    /** @Inject({"baseUrl" = "app.base.url"}) */
    public function __construct(string $baseUrl, Router $router)
    {
        $this->baseUrl = $baseUrl;
        $this->router = $router;
    }

    public function __invoke(Request $request): void
    {
        // router apply (with baseUrl calculation)
        $requestUri = $request->getServer()->requestUri;

        if (strpos($requestUri, $this->baseUrl)) {
            $requestUri = substr($requestUri, strlen($this->baseUrl));
        }

        $request->setParams($this->router->decodeUrl($requestUri));

        if (!$request->module) {
            $request
                ->setModuleName('mmi')
                ->setControllerName('index')
                ->setActionName('error');
        }
    }
}
