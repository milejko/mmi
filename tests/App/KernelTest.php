<?php
declare(strict_types=1);

namespace App;

use Mmi\App\App;
use Mmi\Http\Request;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Mmi\IndexController
 */
final class KernelTest extends TestCase
{
    public function testDefaultResponseIsReturnedForEmptyRequest(): void
    {
        $app = new App(new Request());

        $response = $app->handleRequest();
        $responseContent = $response->getContent();

        self::assertEquals(200, $response->getCode());
        self::assertStringContainsString('It works!', $responseContent);
    }

    public function testExampleResponseIsReturnedForMatchingRequest(): void
    {
        $app = new App(self::getRequestForExample());

        $response = $app->handleRequest();
        $responseContent = $response->getContent();

        self::assertEquals(200, $response->getCode());
        self::assertStringContainsString('<title>Example Controller</title>', $responseContent);
    }

    private static function getRequestForExample(): Request
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
            'REQUEST_URI' => '/example',
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
