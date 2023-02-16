<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Uppercase;

class UrlencodeTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals('ŻÓŁW', (new Uppercase())->filter('żóŁw'));
        $this->assertEquals('ŚWINKA', (new Uppercase())->filter('śwINka'));
    }
}
