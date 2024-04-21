<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Pad;

class PadTest extends \PHPUnit\Framework\TestCase
{
    public function testIfDefaultPadResolvesTo5SpacesRightPad(): void
    {
        $pad = new Pad();
        self::assertEquals('x    ', $pad->filter('x'));
    }

    public function testIfLeftPaddingWorks(): void
    {
        $pad = (new Pad())
            ->setLength(10)
            ->setPadLeft()
            ->setPadCharacter('-');
        self::assertEquals('---------x', $pad->filter('x'));
    }

    public function testIfRightPaddingWorks(): void
    {
        $pad = (new Pad())
            ->setLength(10)
            ->setPadRight()
            ->setPadCharacter('-');
        self::assertEquals('x---------', $pad->filter('x'));
    }

    public function testIfBothPaddingWorks(): void
    {
        $pad = (new Pad())
            ->setLength(5)
            ->setPadBoth()
            ->setPadCharacter('-');
        self::assertEquals('--x--', $pad->filter('x'));

        $pad = (new Pad())
            ->setLength(6)
            ->setPadBoth()
            ->setPadCharacter('-');
        self::assertEquals('--x---', $pad->filter('x'));
    }

    public function testIfIntPadding(): void
    {
        $pad = (new Pad())
            ->setLength(10)
            ->setPadRight()
            ->setPadCharacter('-');
        self::assertEquals('12--------', $pad->filter(12));
    }
}
