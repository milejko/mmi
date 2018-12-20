<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Http;

use Mmi\Http\RequestFiles;

class RequestFilesTest extends \PHPUnit\Framework\TestCase
{

    private $_sampleFile = ['name' => 'image.png', 'tmp_name' => BASE_PATH . '/tests/data/test.png'];
    private $_sampleIncompleteFile = ['size' => 1];
    private $_anotherIncompleteFile = ['name' => 'image.jpg'];

    public function testSingleUpload()
    {
        $files = new RequestFiles([$this->_sampleFile, $this->_sampleIncompleteFile, $this->_anotherIncompleteFile]);
        $this->assertFalse($files->isEmpty());
        $firstFile = $files->current();
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $firstFile);
        $this->assertEquals('image.png', $firstFile->name);
        //$this->assertFalse(isset($files->current()));
    }

    public function testMultiUpload()
    {
        $files = new RequestFiles([[
            'name' => [
                0 => 'image.png',
                1 => 'image2.png',
                2 => 'image3.png'
            ],
            'type' => [
                0 => 'text/plain',
                1 => 'text/plain'
            ],
            'tmp_name' => [
                0 => BASE_PATH . '/tests/data/test.png',
                1 => BASE_PATH . '/tests/data/test.png',
            ],
            'error' => [
                0 => 0,
                1 => 0
            ],
            'size' => [
                0 => 123,
                1 => 456
            ],
        ]]);
        $this->assertFalse($files->isEmpty());
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $files->__get(0)->__get(0));
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $files->__get(0)->__get(1));
        $this->assertEquals('image2.png', $files->__get(0)->__get(1)->name);
    }

}
