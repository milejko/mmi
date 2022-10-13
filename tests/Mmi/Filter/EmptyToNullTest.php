<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Filter;

use Mmi\Filter\EmptyToNull;

class EmptyToNullTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertNull((new EmptyToNull)->filter(''));
        $this->assertNull((new EmptyToNull)->filter(0));
        $this->assertNull((new EmptyToNull)->filter(null));
        $this->assertNull((new EmptyToNull)->filter([]));
    }

}
