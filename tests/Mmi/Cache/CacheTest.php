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

    public function testNew()
    {
        $emptyHandlerConfig = new CacheConfig;
        $emptyHandlerConfig->handler = null;
        $this->assertInstanceOf('\Mmi\Cache\Cache', $emptyCache = new Cache($emptyHandlerConfig), 'Unable to create cache without handler');
        $this->assertInstanceOf('\Mmi\Cache\CacheConfig', $emptyCache->getConfig());

        $config = new CacheConfig;
        $config->active = 0;
        $this->_testInactiveCache(new Cache($config));
    }

    public function testFileHandlerDistributed()
    {
        $cacheConfig = new CacheConfig;
        $cacheConfig->path = BASE_PATH . '/var/cache';
        $cacheConfig->distributed = true;
        $cache = new Cache($cacheConfig);
        $this->_testActiveCache($cache);
    }

    public function testFileHandler()
    {
        $cacheConfig = new CacheConfig;
        $cacheConfig->path = BASE_PATH . '/var/cache';
        $cache = new Cache($cacheConfig);
        //umieszczanie w buforze uszkodzonego pliku
        file_put_contents($cacheConfig->path . '/test', self::INVALID_CACHE_DATA);
        $this->assertNull($cache->load('test'), 'Broken data');
        $this->_testActiveCache($cache);
    }

    public function testApcHandler()
    {
        if (!function_exists('\apcu_fetch')) {
            return;
        }
        $config = new CacheConfig;
        $config->handler = 'apc';
        $config->distributed = true;
        $cache = new Cache($config);
        $this->_testActiveCache($cache);
    }

    public function testMemcacheHandler()
    {
        if (!class_exists('\Memcache')) {
            return;
        }
        $config = new CacheConfig;
        $config->handler = 'memcache';
        $config->path = '127.0.0.1:11211';
        //próba połączenia
        try {
            (new Cache($config))->flush();
        } catch (\Mmi\App\KernelException $e) {
            return;
        }
        //dodanie ścieżki z opcjami
        $config->path = ['udp://127.0.0.1:11211?timeout=5&weight=1'];
        $cache = new Cache($config);
        $this->_testActiveCache($cache);
    }

    public function testRedisHandler()
    {
        if (!class_exists('\Redis')) {
            return;
        }
        $config = new CacheConfig;
        $config->handler = 'redis';
        $config->path = 'udp://user:pass@127.0.0.1:6379/1';
        //próba połączenia
        try {
            $cache = new Cache($config);
            //połączenie dopiero przy wywołaniu dowolnej metody
            $cache->flush();
        } catch (\RedisException $e) {
            return;
        }
        $this->_testActiveCache($cache);
    }

    /**
     * @expectedException \Mmi\Cache\CacheException
     */
    public function testRedisInvalidPath()
    {
        $this->expectException(\Mmi\Cache\CacheException::class);
        if (!class_exists('\Redis')) {
            throw new \Mmi\Cache\CacheException();
        }
        $config = new CacheConfig;
        $config->handler = 'redis';
        //zła ścieżka
        $config->path = 'surely-invalid-path';
        //tu wyjątek
        (new Cache($config))->flush();
    }

    protected function _testActiveCache(Cache $cache)
    {
        $this->assertNull($cache->flush(), 'Flush should always return null');
        $this->assertLessThan(2, (int) $cache->remove('surely-inexistent-key'));
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
        $newCache = new Cache($cache->getConfig());
        $this->assertNull($newCache->load(self::TEST_KEY));
    }

    protected function _testInactiveCache(Cache $inactiveCache)
    {
        $this->assertTrue($inactiveCache->save(self::TEST_DATA, self::TEST_KEY), 'Inactive cache save should return true');
        $this->assertNull($inactiveCache->load(self::TEST_KEY), 'Inactive cache load should return null');
        $this->assertTrue($inactiveCache->remove('surely-inexistent-key'), 'Remove should always return true');
        $this->assertNull($inactiveCache->flush(), 'Flush should always return null');
    }

}
