<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Filter;

use Mmi\Filter\Intval;

class IntvalTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals(13, (new Intval)->filter(13));
        $this->assertEquals(13, (new Intval)->filter('13'));
        $this->assertEquals(13, (new Intval)->filter('13a'));
    }

}
