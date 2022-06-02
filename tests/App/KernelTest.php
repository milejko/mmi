<?php
declare(strict_types=1);

namespace App;

use Mmi\App\App;
use Mmi\Http\Request;
use PHPUnit\Framework\TestCase;

final class KernelTest extends TestCase
{
    public function testDefaultResponseIsReturnedForEmptyRequest(): void
    {
        $app = new App();

        $response = $app->handleRequest((new Request()));
        $responseContent = $response->getContent();

        self::assertEquals(200, $response->getCode());
        self::assertStringContainsString('MMi Framework', $responseContent);
        self::assertStringContainsString('It works!', $responseContent);
    }
}
