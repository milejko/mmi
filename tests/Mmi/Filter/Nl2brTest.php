<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Filter;

use Mmi\Filter\Nl2br;

class Nl2brTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('test<br />' . "\n" . 'test', (new Nl2br)->filter('test' . "\n" . 'test'));
        $this->assertEquals('test<br />' . "\r\n" . 'test', (new Nl2br)->filter('test' . "\r\n" . 'test'));
    }

}
