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
        $this->assertNull((new KernelProfiler)->event('test'), 'Profiler event method should return null');
    }

    public function testProfiler()
    {
        $profiler = new KernelProfiler;
        $this->assertCount(0, $profiler->get(), 'Profiler should be empty here');
        $profiler->event('test');
        $this->assertCount(1, $profiler->get(), 'Profiler should contain 1 entry');
        $profiler->event('test2');
        $profiler->event('test3');
        $this->assertCount(3, $profiler->get(), 'Profiler should contain 3 entries');
        $this->assertEquals(3, $profiler->count(), 'Count method invalid');
        $this->assertGreaterThan(0, $profiler->elapsed(), 'Elapsed should be greater than 0');
    }

}
