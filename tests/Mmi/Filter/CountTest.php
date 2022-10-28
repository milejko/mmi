<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Count;

class CountTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals(3, (new Count)->filter(['x', 'a', 'b']));
        $this->assertEquals(0, (new Count)->filter([]));
    }
}
