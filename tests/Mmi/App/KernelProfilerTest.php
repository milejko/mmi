<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\KernelProfiler;

/**
 * Test profilera aplikacji
 */
class KernelProfilerTest extends \PHPUnit\Framework\TestCase
{

    public function testEvent()
    {
        $this->assertNull((new KernelProfiler)->event('test'));
    }

    public function testProfiler()
    {
        $profiler = new KernelProfiler;
        $this->assertCount(0, $profiler->get());
        $profiler->event('test');
        $this->assertCount(1, $profiler->get());
        $profiler->event('test2');
        $profiler->event('test3');
        $this->assertCount(3, $profiler->get());
        $this->assertEquals(3, $profiler->count());
        $this->assertGreaterThan(0, $profiler->elapsed());
    }

}
