<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\NullKernelProfiler;

/**
 * Test profilera null
 */
class NullKernelProfilerTest extends \PHPUnit\Framework\TestCase
{

    public function testEvent()
    {
        $this->assertNull((new NullKernelProfiler)->event('test'), 'NullProfiler event method should return null');
    }

    public function testProfiler()
    {
        $profiler = new NullKernelProfiler;
        $this->assertCount(0, $profiler->get(), 'Always 0 for null profiler');
        $profiler->event('test');
        $this->assertCount(0, $profiler->get(), 'Always 0 for null profiler');
        $profiler->event('test2');
        $profiler->event('test3');
        $this->assertCount(0, $profiler->get(), 'Always 0 for null profiler');
        $this->assertEquals(0, $profiler->count(), 'Always 0 for null profiler');
        $this->assertEquals(0, $profiler->elapsed(), 'Always 0 for null profiler');
    }

}
