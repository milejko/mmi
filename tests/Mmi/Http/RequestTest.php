<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Http;

use Mmi\Http\Request;

class RequestTest extends \PHPUnit\Framework\TestCase
{
    public function testGetContentType()
    {
        $this->assertEmpty((new Request)->getContentType());
    }

    public function testGetRequestMethod()
    {
        $this->assertEmpty((new Request)->getRequestMethod());
    }

    public function testGetReferer()
    {
        $this->assertEmpty((new Request)->getReferer());
    }

    public function testGetHeader()
    {
        $this->assertNull((new Request)->getHeader('test'));
    }

    public function testGetPost()
    {
        $this->assertInstanceOf('\Mmi\Http\RequestPost', (new Request)->getPost());
    }

    public function testGetFiles()
    {
        $this->assertInstanceOf('\Mmi\Http\RequestFiles', (new Request)->getFiles());
    }

    public function testGetters()
    {
        $this->assertEmpty((new Request)->getModuleName());
        $this->assertEmpty((new Request)->getControllerName());
        $this->assertEmpty((new Request)->getActionName());
        $request = (new Request)->setModuleName('tm')
            ->setControllerName('tc')
            ->setActionName('ta');
        $this->assertEquals('tm', $request->getModuleName());
        $this->assertEquals('tc', $request->getControllerName());
        $this->assertEquals('ta', $request->getActionName());
        $this->assertEquals('tm', $request->module);
        $this->assertEquals('tc', $request->controller);
        $this->assertEquals('ta', $request->action);
        $this->assertEquals('tm:tc:ta', $request->getAsColonSeparatedString());
    }
}
