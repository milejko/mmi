<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Escape;

class EscapeTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('', (new Escape)->filter('<script>'));
        $this->assertEquals('', (new Escape)->filter('<script></script>'));
        $this->assertEquals('abcabc', (new Escape)->filter('abc<script></script>abc'));
    }

}
