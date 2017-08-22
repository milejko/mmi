<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Http;

use Mmi\Http\RequestFile;

class RequestFileTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @expectedException \Mmi\Http\HttpException
     */
    public function testEmptyObject()
    {
        new RequestFile;
    }

    /**
     * @expectedException \Mmi\Http\HttpException
     */
    public function testMissingTmpName()
    {
        new RequestFile(['name' => 'image.png']);
    }

    /**
     * @expectedException \Mmi\Http\HttpException
     */
    public function testNew()
    {
        $file = new RequestFile(['name' => 'image.png', 'tmp_name' => BASE_PATH . '/tests/data/test.png']);
        $this->assertEquals('image.png', $file->name);
        $this->assertEquals(BASE_PATH . '/tests/data/test.png', $file->tmpName);
        $this->assertEquals('image/png', $file->type);
        $this->assertEquals('26271', $file->size);
        //wyjątek
        new RequestFile(['name' => '???', 'tmp_name' => 'surely-nonexistent-path']);
    }

}
