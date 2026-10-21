<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Cache;

use Mmi\Cache\Cache;
use Mmi\Cache\CacheConfig;

/**
 * Test bufora aplikacji
 */
class CacheTest extends \PHPUnit\Framework\TestCase
{
    public const TEST_KEY = 'php-unit-test';
    public const TEST_DATA = 'unit-test-php';
    public const INVALID_CACHE_DATA = 'a:2:{s:1:"x";s:13:"unit-test-php";s:1:"e";i:1503324942;}';

    protected $_backends = ['file', 'redis'];

    public function testNew()
    {
        $emptyHandlerConfig = new CacheConfig();
        $emptyHandlerConfig->handler = null;
        $this->assertInstanceOf('\Mmi\Cache\Cache', $emptyCache = new Cache($emptyHandlerConfig), 'Unable to create cache without handler');
        $this->assertInstanceOf('\Mmi\Cache\CacheConfig', $emptyCache->getConfig());

        $config = new CacheConfig();
        $config->active = 0;
        $this->_testInactiveCache(new Cache($config));
    }

    public function testFileHandler()
    {
        $cacheConfig = new CacheConfig();
        $cacheConfig->path = BASE_PATH . '/var/cache';
        $cacheConfig->active = true;
        $cache = new Cache($cacheConfig);
        //umieszczanie w buforze uszkodzonego pliku
        file_put_contents($cacheConfig->path . '/test', self::INVALID_CACHE_DATA);
        $this->assertNull($cache->load('test'), 'Broken data');
        $this->_testActiveCache($cache);
    }

    public function testRedisHandler()
    {
        $this->assertTrue(class_exists('\Redis'));

        $config = new CacheConfig();
        $config->handler = 'redis';
        $config->path = 'udp://user:pass@127.0.0.1:6379/1';
        $config->active = true;
        //próba połączenia
        try {
            $cache = new Cache($config);
            //połączenie dopiero przy wywołaniu dowolnej metody
            $cache->flush();
        } catch (\RedisException $e) {
            return;
        }
        $this->_testActiveCache($cache);
        $this->assertTrue($cache->save(self::TEST_DATA, self::TEST_KEY));
        //completely new cache object (no registry memory)
        $cache = new Cache($config);
        $this->assertEquals($cache->load(self::TEST_KEY), self::TEST_DATA);
    }

    public function testRegistryOffSeesValuesChangedByAnotherInstance()
    {
        $path = $this->_createCachePath();
        $cache = $this->_createFileCache($path, false);
        $otherProcess = $this->_createFileCache($path, true);

        $this->assertTrue($cache->save('first', self::TEST_KEY));
        $this->assertEquals('first', $cache->load(self::TEST_KEY));
        $this->assertTrue($otherProcess->save('second', self::TEST_KEY));
        $this->assertEquals('second', $cache->load(self::TEST_KEY));
        $this->assertTrue($otherProcess->remove(self::TEST_KEY));
        $this->assertNull($cache->load(self::TEST_KEY));
        $this->_removeCachePath($path);
    }

    public function testRegistryOffKeepsNothingInTheRegistry()
    {
        $path = $this->_createCachePath();
        $cache = $this->_createFileCache($path, false);
        $otherProcess = $this->_createFileCache($path, true);

        $this->assertTrue($cache->save(self::TEST_DATA, 'saved-here'));
        $this->assertTrue($otherProcess->save(self::TEST_DATA, 'saved-elsewhere'));
        $this->assertEquals(self::TEST_DATA, $cache->load('saved-elsewhere'));
        //registry back on: anything kept by save() or by the backend read would be served after removal
        $cache->getConfig()->useRegistry = true;
        $this->assertTrue($otherProcess->remove('saved-here'));
        $this->assertTrue($otherProcess->remove('saved-elsewhere'));
        $this->assertNull($cache->load('saved-here'));
        $this->assertNull($cache->load('saved-elsewhere'));
        $this->_removeCachePath($path);
    }

    public function testRegistryOnServesTheProcessCopy()
    {
        $this->assertTrue((new CacheConfig())->useRegistry, 'The registry is on by default');
        $path = $this->_createCachePath();
        $cache = $this->_createFileCache($path, true);
        $otherProcess = $this->_createFileCache($path, true);

        $this->assertTrue($cache->save('first', self::TEST_KEY));
        $this->assertTrue($otherProcess->save('second', self::TEST_KEY));
        $this->assertEquals('first', $cache->load(self::TEST_KEY));
        $this->assertEquals('second', $otherProcess->load(self::TEST_KEY));
        $this->_removeCachePath($path);
    }

    protected function _createFileCache(string $path, bool $useRegistry): Cache
    {
        $config = new CacheConfig();
        $config->path = $path;
        $config->active = true;
        $config->useRegistry = $useRegistry;
        return new Cache($config);
    }

    protected function _createCachePath(): string
    {
        $path = BASE_PATH . '/var/cache/registry-test-' . uniqid();
        mkdir($path, 0777, true);
        return $path;
    }

    protected function _removeCachePath(string $path): void
    {
        array_map('unlink', glob($path . '/*'));
        rmdir($path);
    }

    protected function _testActiveCache(Cache $cache)
    {
        $this->assertNull($cache->flush(), 'Flush should always return null');
        $this->assertLessThan(2, (int)$cache->remove('surely-inexistent-key'));
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
        $this->assertTrue($cache->remove(self::TEST_KEY), 'Remove should always return true');
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
