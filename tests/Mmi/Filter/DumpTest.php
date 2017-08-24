<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\Dump;

class DumpTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        foreach (['test', ['test'], ['test' => 'test'], new \stdClass()] as $value) {
            $this->assertEquals('<pre>' . print_r($value, true) . '</pre>', (new Dump)->filter($value));
        }
    }

}
