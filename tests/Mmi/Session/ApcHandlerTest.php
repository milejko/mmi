<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Session;

use Mmi\Session\ApcHandler;

/**
 * Test handlera plikowego
 */
class ApcHandlerTest extends \PHPUnit\Framework\TestCase
{

    const FILE_PATH = BASE_PATH . '/var/session/sess-';

    public function testOpen()
    {
        if (!function_exists('\apcu_fetch')) {
            return;
        }
        $this->assertTrue((new ApcHandler())->open('test', 'test'));
    }

    public function testRead()
    {
        if (!function_exists('\apcu_fetch')) {
            return;
        }
        $fh = new ApcHandler();
        $this->assertEquals('', $fh->read('abc'));
        $sessionId = md5(microtime());
        $this->assertEquals('', $fh->read($sessionId));
    }

    public function testClose()
    {
        if (!function_exists('\apcu_fetch')) {
            return;
        }
        $this->assertTrue((new ApcHandler())->close());
    }

    public function testGc()
    {
        if (!function_exists('\apcu_fetch')) {
            return;
        }
        $fh = new ApcHandler();
        $this->assertFalse($fh->gc(0));
    }
}
