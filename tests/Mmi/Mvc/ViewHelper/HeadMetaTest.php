<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Mvc\ViewHelper;

use Mmi\App\TestApp;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\HeadMeta;

class HeadMetaTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $hm = new HeadMeta(new View(TestApp::$di));
        $hm->openGraph('test', 'test', false, '?');
        $this->assertInstanceOf('\Mmi\Mvc\ViewHelper\HeadMeta', $hm->headMeta());
        $this->assertEquals('<!--[if ?]>	<meta property="test" content="test" /><![endif]-->' . "\n", (string) $hm);
    }

}
