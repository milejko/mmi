<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Ceil;

class CeilTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals(14, (new Ceil())->filter(13.3));
        $this->assertEquals(124, (new Ceil())->filter(123.78));
    }
}
