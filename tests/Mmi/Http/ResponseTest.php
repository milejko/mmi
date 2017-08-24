<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Http;

use Mmi\Http\Response;

class ResponseTest extends \PHPUnit\Framework\TestCase
{

    CONST CLASS_NAME = '\Mmi\Http\Response';

    public function testGetSetType()
    {
        $response = new Response;
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypeGzip());
        $this->assertEquals('application/x-gzip', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypeJpeg());
        $this->assertEquals('image/jpeg', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypeJson());
        $this->assertEquals('application/json', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypePng());
        $this->assertEquals('image/png', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypePlain());
        $this->assertEquals('text/plain', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypeJs());
        $this->assertEquals('application/x-javascript', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypeXml());
        $this->assertEquals('text/xml', $response->getType());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setTypeHtml());
        $this->assertEquals('text/html', $response->getType());
    }

    public function testGetSetCode()
    {
        $response = new Response;
        $this->assertInstanceOf(self::CLASS_NAME, $response->setCodeOk());
        $this->assertEquals(200, $response->getCode());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setCodeUnauthorized());
        $this->assertEquals(401, $response->getCode());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setCodeForbidden());
        $this->assertEquals(403, $response->getCode());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setCodeNotFound());
        $this->assertEquals(404, $response->getCode());
        $this->assertInstanceOf(self::CLASS_NAME, $response->setCodeError());
        $this->assertEquals(500, $response->getCode());
    }

    /**
     * @expectedException \Mmi\Http\HttpException
     */
    public function testFailedCode()
    {
        $response = new Response;
        $this->assertInstanceOf(self::CLASS_NAME, $response->setCode(792813));
    }

    public function testGetSetDebug()
    {
        $response = new Response;
        $this->assertInstanceOf(self::CLASS_NAME, $response->setDebug());
    }

    public function testSetContent()
    {
        $response = new Response;
        $response->setContent('test content')
            ->setDebug();
        $view = \Mmi\App\FrontController::getInstance()->getView();
        $view->sampleVariable = ['test1' => 'test', 'test2' => ['test1' => 'test', 'test' => ['test' => 'test', 'test2' => [1]]]];
        $view->anotherVariable = 'test';
        $this->assertEquals('test content', $response->getContent());
        ob_start();
        $this->assertNull($response->send());
        $this->assertEquals('test content', ob_get_contents());
        ob_end_clean();
    }

    public function testGetSetHeaders()
    {
        $response = new Response;
        $response->setCodeNotFound()
            ->setTypeGzip();
        $this->assertCount(2, $response->getHeaders());
        foreach ($response->getHeaders() as $header) {
            $this->assertInstanceOf('\Mmi\Http\ResponseHeader', $header);
        }
        $this->assertNull($response->send());
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testRedirect()
    {
        $response = new Response;
        $response->redirect('mmi', 'index', 'index', [], false);
    }

}
