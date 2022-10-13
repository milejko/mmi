<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Http;

use Mmi\Http\RequestFiles;

class RequestFilesTest extends \PHPUnit\Framework\TestCase
{

    private $_sampleFile = ['name' => 'image.png', 'tmp_name' => BASE_PATH . '/tests/data/test.png'];
    private $_sampleIncompleteFile = ['size' => 1];
    private $_anotherIncompleteFile = ['name' => 'image2.jpg'];

    public function testSingleUpload()
    {
        $rf = new RequestFiles(['field1' => $this->_sampleFile, 'field2' => $this->_sampleIncompleteFile, 'field3' => $this->_anotherIncompleteFile]);
        $this->assertFalse($rf->isEmpty());
        $firstFile = $rf->getAsArray()['field1'][0];
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $firstFile);
        $this->assertEquals('image.png', $firstFile->name);
    }

    public function testMultiUpload()
    {
        $rf = new RequestFiles(['fieldName' => [
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
        $this->assertFalse($rf->isEmpty());
        $files = $rf->getAsArray();
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $files['fieldName'][0]);
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $files['fieldName'][1]);
        $this->assertEquals('image2.png', $files['fieldName'][1]->name);
    }

    public function testNestedForms()
    {
        $rf = new RequestFiles([
            'formname' => [
                'name' => ['field1' => 'image.jpg'],
                'tmp_name' => ['field1' => BASE_PATH . '/tests/data/test.png'],
            ]
        ]);
        $this->assertFalse($rf->isEmpty());
        $files = $rf->getAsArray();
        $firstFile = $files['formname']['field1'][0];
        $this->assertInstanceOf('\Mmi\Http\RequestFile', $firstFile);
        $this->assertEquals('image.jpg', $firstFile->name);
    }
}
