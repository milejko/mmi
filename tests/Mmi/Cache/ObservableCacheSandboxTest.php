<?php

declare(strict_types=1);

namespace Tests\Mmi\Cache;

use Mmi\Cache\ObservableCacheSandbox;
use PHPUnit\Framework\TestCase;

final class ObservableCacheSandboxTest extends TestCase
{
    public function testIfEmptyCacheReturnsNoValues(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();

        self::assertEmpty($cacheSandbox->load('some-key'));
        self::assertEquals(['load'], $cacheSandbox->getEventLog());
    }

    public function testIfValuesCanBeStoredLoadedAndRemoved(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();

        self::assertTrue($cacheSandbox->save([], 'other-key'));
        self::assertEquals([], $cacheSandbox->load('other-key'));
        self::assertEquals(['save', 'load'], $cacheSandbox->getEventLog());

        self::assertTrue($cacheSandbox->remove('other-key'));
        self::assertEmpty($cacheSandbox->load('other-key'));
        self::assertEquals(['save', 'load', 'remove', 'load'], $cacheSandbox->getEventLog());
    }

    public function testIfValuesCanStoredLoadedAndFlushed(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();
        self::assertTrue($cacheSandbox->save([], 'key'));
        self::assertTrue($cacheSandbox->save(new self(), 'another-key'));
        self::assertEquals(['save', 'save'], $cacheSandbox->getEventLog());

        self::assertEquals([], $cacheSandbox->load('key'));
        self::assertInstanceOf(self::class, $cacheSandbox->load('another-key'));
        self::assertEquals(['save', 'save', 'load', 'load'], $cacheSandbox->getEventLog());

        $cacheSandbox->flush();
        self::assertEquals(['save', 'save', 'load', 'load', 'flush'], $cacheSandbox->getEventLog());

        self::assertEmpty($cacheSandbox->load('key'));
        self::assertEmpty($cacheSandbox->load('another-key'));
        self::assertEquals(['save', 'save', 'load', 'load', 'flush', 'load', 'load'], $cacheSandbox->getEventLog());
    }

    public function testIfEventLogCanBeFlushed(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();

        self::assertEmpty($cacheSandbox->load('some-key'));
        self::assertEquals(['load'], $cacheSandbox->getEventLog());
        $cacheSandbox->flushEventLog();
        self::assertEmpty($cacheSandbox->getEventLog());
    }

    public function testIfIsActiveGivesTrue(): void
    {
        self::assertTrue((new ObservableCacheSandbox())->isActive());
    }
}
