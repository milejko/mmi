<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Cache;

use Mmi\Cache\Cache,
    Mmi\Cache\CacheConfig;

/**
 * Test kernela aplikacji
 */
class CacheTest extends \PHPUnit\Framework\TestCase
{

    CONST TEST_KEY = 'php-unit-test';
    CONST TEST_DATA = 'unit-test-php';
    CONST INVALID_CACHE_DATA = 'a:2:{s:1:"x";s:13:"unit-test-php";s:1:"e";i:1503324942;}';

    protected $_backends = ['file', 'apc', 'memcache', 'redis'];

    public function testConstruct()
    {
        $emptyHandlerConfig = new CacheConfig;
        $emptyHandlerConfig->handler = null;
        $this->assertInstanceOf('\Mmi\Cache\Cache', $emptyCache = new Cache($emptyHandlerConfig), 'Unable to create cache without handler');
        $this->assertInstanceOf('\Mmi\Cache\CacheConfig', $emptyCache->getConfig());
        $this->assertInstanceOf('\Mmi\Cache\CacheRegistry', $emptyCache->getRegistry());

        $config = new CacheConfig;
        $config->active = 0;
        $this->_testInactiveCache(new Cache($config));
    }

    public function testFileBackend()
    {
        $this->assertInstanceOf('\Mmi\Cache\Cache', $fileCache = new Cache(new CacheConfig), 'Unable to create Cache with default cache handler');
        //umieszczanie w buforze uszkodzonego pliku
        file_put_contents((new CacheConfig)->path . '/test', self::INVALID_CACHE_DATA);
        $this->assertNull($fileCache->load('test'), 'Broken data');
        $this->_testActiveCache($fileCache);
    }

    public function testApcBackend()
    {
        if (!function_exists('\apcu_fetch')) {
            return;
        }
        $config = new CacheConfig;
        $config->handler = 'apc';
        $config->distributed = true;
        $apcCache = new Cache($config);
        $this->_testActiveCache($apcCache);
    }

    protected function _testActiveCache(Cache $cache)
    {
        //nieistniejący klucz
        $this->assertNull($cache->load('surely-inexistent-key'));
        //null
        $this->assertTrue($cache->save(null, self::TEST_KEY));
        $this->assertNull($cache->load(self::TEST_KEY));
        //0
        $this->assertTrue($cache->save(0, self::TEST_KEY));
        $this->assertEquals(0, $cache->load(self::TEST_KEY));
        //15
        $this->assertTrue($cache->save(15, self::TEST_KEY));
        $this->assertEquals(15, $cache->load(self::TEST_KEY));
        //save / remove
        $this->assertTrue($cache->save(self::TEST_DATA, self::TEST_KEY));
        $this->assertEquals(self::TEST_DATA, $cache->load(self::TEST_KEY));
        $this->assertTrue($cache->remove(self::TEST_KEY), 'Remove should always return true');
        $this->assertNull($cache->load(self::TEST_KEY));
        $this->assertTrue($cache->save(self::TEST_DATA, self::TEST_KEY));
        $this->assertEquals(self::TEST_DATA, $cache->load(self::TEST_KEY));
        $this->assertNull($cache->flush(), 'Flush should always return null');
        $this->assertNull($cache->load(self::TEST_KEY));
        $this->assertTrue($cache->save(self::TEST_DATA, self::TEST_KEY, 1));
        sleep(1);
        //czyszczenie rejestru
        $cache->getRegistry()->setOptions([], true);
        //po 1 sekundzie znika
        $this->assertNull($cache->load(self::TEST_KEY));
    }

    protected function _testInactiveCache(Cache $inactiveCache)
    {
        $this->assertTrue($inactiveCache->save(self::TEST_DATA, self::TEST_KEY), 'Inactive cache save should return true');
        $this->assertNull($inactiveCache->load(self::TEST_KEY), 'Inactive cache load should return null');
        $this->assertTrue($inactiveCache->remove('surely-inexistent-key'), 'Remove should always return true');
        $this->assertNull($inactiveCache->flush(), 'Flush should always return null');
    }

}
