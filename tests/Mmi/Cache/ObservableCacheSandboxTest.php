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

        $this->assertEmpty($cacheSandbox->load('some-key'));
        $this->assertEquals(['load'], $cacheSandbox->getEventLog());
    }

    public function testIfValuesCanBeStoredLoadedAndRemoved(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();

        $this->assertTrue($cacheSandbox->save([], 'other-key'));
        $this->assertEquals([], $cacheSandbox->load('other-key'));
        $this->assertEquals(['save', 'load'], $cacheSandbox->getEventLog());

        $this->assertTrue($cacheSandbox->remove('other-key'));
        $this->assertEmpty($cacheSandbox->load('other-key'));
        $this->assertEquals(['save', 'load', 'remove', 'load'], $cacheSandbox->getEventLog());
    }

    public function testIfValuesCanStoredLoadedAndFlushed(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();
        $this->assertTrue($cacheSandbox->save([], 'key'));
        $this->assertTrue($cacheSandbox->save(new \stdClass(), 'another-key'));
        $this->assertEquals(['save', 'save'], $cacheSandbox->getEventLog());

        $this->assertEquals([], $cacheSandbox->load('key'));
        $this->assertInstanceOf(\stdClass::class, $cacheSandbox->load('another-key'));
        $this->assertEquals(['save', 'save', 'load', 'load'], $cacheSandbox->getEventLog());

        $cacheSandbox->flush();
        $this->assertEquals(['save', 'save', 'load', 'load', 'flush'], $cacheSandbox->getEventLog());

        $this->assertEmpty($cacheSandbox->load('key'));
        $this->assertEmpty($cacheSandbox->load('another-key'));
        $this->assertEquals(['save', 'save', 'load', 'load', 'flush', 'load', 'load'], $cacheSandbox->getEventLog());
    }

    public function testIfEventLogCanBeFlushed(): void
    {
        $cacheSandbox = new ObservableCacheSandbox();

        $this->assertEmpty($cacheSandbox->load('some-key'));
        $this->assertEquals(['load'], $cacheSandbox->getEventLog());
        $cacheSandbox->flushEventLog();
        $this->assertEmpty($cacheSandbox->getEventLog());
    }

    public function testIfIsActiveGivesTrue(): void
    {
        $this->assertTrue((new ObservableCacheSandbox())->isActive());
    }
}
