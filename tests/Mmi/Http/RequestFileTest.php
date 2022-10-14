<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Http;

use Mmi\Http\RequestFile;

class RequestFileTest extends \PHPUnit\Framework\TestCase
{

    public function testEmptyObject()
    {
        $this->expectException(\Mmi\Http\HttpException::class);
        new RequestFile;
    }

    /**
     * @expectedException \Mmi\Http\HttpException
     */
    public function testMissingTmpName()
    {
        $this->expectException(\Mmi\Http\HttpException::class);
        new RequestFile(['name' => 'image.png']);
    }

    public function testNew()
    {
        $this->expectException(\Mmi\Http\HttpException::class);
        $file = new RequestFile(['name' => 'image.png', 'tmp_name' => BASE_PATH . '/tests/data/test.png']);
        $this->assertEquals('image.png', $file->name);
        $this->assertEquals(BASE_PATH . '/tests/data/test.png', $file->tmpName);
        $this->assertEquals('image/png', $file->type);
        $this->assertEquals('26271', $file->size);
        //wyjątek
        new RequestFile(['name' => '???', 'tmp_name' => 'surely-nonexistent-path']);
    }

}
