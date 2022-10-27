<?php
declare(strict_types=1);

namespace Mmi\Mvc;

use Mmi\Http\Request;
use Mmi\TestKit\TestRouterConfig;
use PHPUnit\Framework\TestCase;

final class RouterApplyTest extends TestCase
{
    public function testItAppliesDefaultParametersForRequest(): void
    {
        $routerApply = new RouterApply('hxxp://example.com', new Router(new RouterConfig()));

        $request = self::getRequestForHelp();
        $routerApply($request);

        self::assertEquals(
            ['module' => 'mmi', 'controller' => 'index', 'action' => 'error'],
            $request->toArray()
        );
    }

    public function testItAppliesParametersForRequestWithTestRouterConfig(): void
    {
        $routerApply = new RouterApply('hxxp://example.com', new Router(new TestRouterConfig()));

        $request = new Request();
        $routerApply($request);

        self::assertEquals(
            ['module' => 'mmi', 'controller' => 'index', 'action' => 'index', 'uri' => '/'],
            $request->toArray()
        );
    }

    public function testForConfiguredRoute(): void
    {
        $routerApply = new RouterApply(
            'hxxp://example.com',
            new Router(
                (new RouterConfig())
                    ->setRoute(
                        'help',
                        'help',
                        ['module' => 'content', 'controller' => 'help', 'action' => 'index']
                    )
            )
        );

        $request = self::getRequestForHelp();
        $routerApply($request);

        self::assertEquals([
                'module' => 'content',
                'controller' => 'help',
                'action' => 'index'
            ],
            $request->toArray()
        );
    }

    private static function getRequestForHelp(): Request
    {
        return new Request([], [], [], [], [], [
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:100.0) Gecko/20100101 Firefox/100.0',
            'HTTP_HOST' => 'example.com',
            'SERVER_NAME' => 'example.com',
            'SERVER_PORT' => '80',
            'SERVER_ADDR' => '172.18.0.3',
            'REMOTE_ADDR' => '172.18.0.1',
            'REQUEST_SCHEME' => 'http',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'DOCUMENT_ROOT' => '/mnt/app/cms-backend/web',
            'DOCUMENT_URI' => '/index.php',
            'REQUEST_URI' => '/help',
            'CONTENT_LENGTH' => '',
            'CONTENT_TYPE' => '',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'SCRIPT_NAME' => '/index.php',
            'SCRIPT_FILENAME' => '/mnt/app/cms-backend/web/index.php',
            'PHP_SELF' => '/index.php',
            'REQUEST_TIME_FLOAT' => 1654162989.415059,
            'REQUEST_TIME' => 1654162989,
        ]);
    }
}
