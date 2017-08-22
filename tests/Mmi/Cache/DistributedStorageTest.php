<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Cache;

use Mmi\Cache\DistributedStorage;

/**
 * Test kernela aplikacji
 */
class DistributedStorageTest extends \PHPUnit\Framework\TestCase
{

    //klucz testowy
    CONST TEST_KEY = 'distributed-test-key';
    //dane testowe
    CONST TEST_DATA = 'distributed-test-data';

    public function testNew()
    {
        for ($i = 0; $i < (4 * DistributedStorage::GARBAGE_COLLECTOR_DIVISOR); $i++) {
            $this->assertInstanceOf('\Mmi\Cache\DistributedStorage', new DistributedStorage);
        }
    }

    public function testSave()
    {
        //zapis testowych danych
        $this->assertTrue((new DistributedStorage)->save(self::TEST_DATA, self::TEST_KEY));
    }

    /**
     * @depends testSave
     */
    public function testGc()
    {
        $this->assertEquals(self::TEST_DATA, (new DistributedStorage)->getOption(self::TEST_KEY));
        $this->assertNull((new DistributedStorage)->gc());
        $this->assertNull((new DistributedStorage)->getOption(self::TEST_KEY));
    }

}
