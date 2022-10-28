<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Mvc\ViewHelper;

use Mmi\App\TestApp;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\HeadStyle;

class HeadStyleTest extends \PHPUnit\Framework\TestCase
{
    public function testClass()
    {
        $hs = new HeadStyle(new View(TestApp::$di));
        $hs->appendStyle('http://www.wp.pl/');
        //już dodany
        $hs->prependStyle('http://www.wp.pl/');
        $hs->prependStyle('http://www.onet.pl/');
        $hs->headStyle(['type' => 'text/css', 'style' => 'http://www.onet.pl']);
        $hs->appendStyle('http://www.google.pl/', [], 'ie');
        $this->assertInstanceOf('\Mmi\Mvc\ViewHelper\HeadStyle', $hs->headStyle());
        $this->assertEquals('<style type="text/css" >
/* <![CDATA[ */
http://www.onet.pl/
/* ]]> */</style><style type="text/css" >
/* <![CDATA[ */
http://www.wp.pl/
/* ]]> */</style><style type="text/css" >
/* <![CDATA[ */
http://www.onet.pl
/* ]]> */</style><!--[if ie]><style type="text/css" >
/* <![CDATA[ */
http://www.google.pl/
/* ]]> */</style><![endif]-->', (string) $hs);
    }
}
