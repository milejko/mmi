<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Mvc\ViewHelper;

use Mmi\App\TestApp;
use Mmi\Mvc\ViewHelper\HeadLink;
use Mmi\Mvc\View;

class HeadLinkTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $hl = new HeadLink(new View(TestApp::$di));
        $hl->appendStylesheet('http://www.wp.pl/', 'print');
        //nie doda się
        $hl->appendStylesheet('http://www.wp.pl/', 'print');
        $hl->prependStylesheet('http://www.onet.pl/');
        $hl->headLink(['rel' => 'stylesheet', 'type' => 'text/css', 'href' => 'http://www.amazon.com/', 'ts' => 1234]);
        $hl->appendAlternate('http://www.google.com/', 'text/html', 'title', 'print');
        $hl->prependAlternate('http://www.google.pl/', 'text/html', 'title');
        $hl->appendCanonical('http://www.example.com/');
        $hl->prependCanonical('http://www.example.pl/');
        $this->assertInstanceOf('\Mmi\Mvc\ViewHelper\HeadLink', $hl->headLink());
        $this->assertEquals('	<link rel="canonical" href="http://www.example.com/" />	<link rel="alternate" type="text/html" title="title" href="http://www.google.com/" media="print" />	<link rel="stylesheet" type="text/css" href="http://www.onet.pl/" />	<link rel="stylesheet" type="text/css" href="http://www.wp.pl/" media="print" />	<link rel="stylesheet" type="text/css" href="http://www.amazon.com/?ts=1234" />	<link rel="alternate" type="text/html" title="title" href="http://www.google.pl/" />	<link rel="canonical" href="http://www.example.pl/" />', (string) $hl);
    }

}
