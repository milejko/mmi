<?php

declare(strict_types=1);

namespace Tests\Mmi\Cache;

use PHPUnit\Framework\TestCase;

final class ObservableCacheStubTest extends TestCase
{
    public function testIfEmptyCacheReturnsNoValues(): void
    {
        $cacheStub = new ObservableCacheStub;

        self::assertEmpty($cacheStub->load('some-key'));
        self::assertEquals(['load'], $cacheStub->getEventLog());
    }

    public function testIfValuesCanBeStoredLoadedAndRemoved(): void
    {
        $cacheStub = new ObservableCacheStub;

        self::assertTrue($cacheStub->save([], 'other-key'));
        self::assertEquals([], $cacheStub->load('other-key'));
        self::assertEquals(['save', 'load'], $cacheStub->getEventLog());

        self::assertTrue($cacheStub->remove('other-key'));
        self::assertEmpty($cacheStub->load('other-key'));
        self::assertEquals(['save', 'load', 'remove', 'load'], $cacheStub->getEventLog());
    }

    public function testIfValuesCanStoredLoadedAndFlushed(): void
    {
        $cacheStub = new ObservableCacheStub;
        self::assertTrue($cacheStub->save([], 'key'));
        self::assertTrue($cacheStub->save(new self, 'another-key'));
        self::assertEquals(['save', 'save'], $cacheStub->getEventLog());

        self::assertEquals([], $cacheStub->load('key'));
        self::assertInstanceOf(self::class, $cacheStub->load('another-key'));
        self::assertEquals(['save', 'save', 'load', 'load'], $cacheStub->getEventLog());

        $cacheStub->flush();
        self::assertEquals(['save', 'save', 'load', 'load', 'flush'], $cacheStub->getEventLog());

        self::assertEmpty($cacheStub->load('key'));
        self::assertEmpty($cacheStub->load('another-key'));
        self::assertEquals(['save', 'save', 'load', 'load', 'flush', 'load', 'load'], $cacheStub->getEventLog());
    }

    public function testIfEventLogCanBeFlushed(): void
    {
        $cacheStub = new ObservableCacheStub;

        self::assertEmpty($cacheStub->load('some-key'));
        self::assertEquals(['load'], $cacheStub->getEventLog());
        $cacheStub->flushEventLog();
        self::assertEmpty($cacheStub->getEventLog());
    }

    public function testIfIsActiveGivesTrue(): void
    {
        self::assertTrue((new ObservableCacheStub)->isActive());
    }
}
