<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Mvc\ViewHelper;

use Mmi\Mvc\ViewHelper\HeadMeta;

class HeadMetaTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $hm = new HeadMeta;
        $hm->openGraph('test', 'test', false, '?');
        $this->assertInstanceOf('\Mmi\Mvc\ViewHelper\HeadMeta', $hm->headMeta());
        $this->assertEquals('<!--[if ?]>	<meta property="test" content="test" /><![endif]-->' . "\n", (string) $hm);
    }

}
