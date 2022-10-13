<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

use Mmi\App\TestApp;
use Mmi\Mvc\View;
use Mmi\Mvc\ViewHelper\HeadScript;

class HeadScriptTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $hs = new HeadScript(new View(TestApp::$di));
        $hs->appendFile('http://www.wp.pl/');
        $hs->headScript(['type' => 'text/javascript', 'src' => 'http://www.onet.pl', 'ts' => 1]);
        //już dodany
        $hs->appendFile('http://www.wp.pl/');
        $hs->appendScript('<script>alert(\'x\');</script>');
        $this->assertInstanceOf('\Mmi\Mvc\ViewHelper\HeadScript', $hs->headScript());
        $this->assertEquals('	<script type="text/javascript" src="http://www.wp.pl/" ></script>' . "\n" .
            '	<script type="text/javascript" src="http://www.onet.pl?ts=1" ></script>' . "\n" .
            '	<script type="text/javascript" >' . "\n" .
            '// <![CDATA[' . "\n" .
            '<script>alert(\'x\');</script>' . "\n" .
            '// ]]></script>' . "\n", (string) $hs);
    }

}
