<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests;

use Mmi\Translate\Translate;

/**
 * Test obiektu tłumaczeń
 */
class TranslateTest extends \PHPUnit\Framework\TestCase
{
    public function testAddTranslation()
    {
        $translate = new Translate();
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/Mock/pl.ini', 'pl'));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/Mock/de.ini', 'de'));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile('/not-important', ''));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/Mock/pl-extended.ini', 'pl'));
        return $translate;
    }

    /**
     * @depends testAddTranslation
     * @param Translate $translate
     */
    public function testSetLocale(Translate $translate)
    {
        $this->assertInstanceOf(Translate::class, $translate->setLocale('pl'));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/Mock/en.ini', 'en'));
        $this->assertEquals('pl', $translate->getLocale());
    }

    /**
     * @depends testAddTranslation
     */
    public function testTranslate(Translate $translate)
    {
        $this->assertEquals('chłopiec', $translate->translate('boy'));
        $this->assertEquals('dziewczyna', $translate->translate('girl'));
        $this->assertEquals('żółw', $translate->translate('turtle'));
        $this->assertEquals('cow', $translate->translate('cow'), 'Inexistent translation should return default value');

        $this->assertInstanceOf(Translate::class, $translate->setLocale('de'));
        $this->assertEquals('Junge', $translate->translate('boy'));
        $this->assertEquals('girl', $translate->translate('girl'));
        $this->assertEquals('Kuh', $translate->translate('cow'));

        $this->assertInstanceOf(Translate::class, $translate->setLocale('en'));
        $this->assertEquals('boy', $translate->translate('boy'));
        $this->assertEquals('gril', $translate->translate('girl'));
        $this->assertEquals('cow', $translate->translate('cow'));

        $this->assertEquals('translation with 1 test and second-test', $translate->translate('test', [1, 'test', 'second-test']));
    }
}
