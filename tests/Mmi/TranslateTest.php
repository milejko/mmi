<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test;

use Mmi\Translate\Translate;

/**
 * Test obiektu tłumaczeń
 */
class TranslateTest extends \PHPUnit\Framework\TestCase
{

    public function testAddTranslation()
    {
        $translate = new Translate();
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/data/pl.ini', 'pl'));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/data/de.ini', 'de'));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile('/not-important', ''));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/data/pl-extended.ini', 'pl'));
        return $translate;
    }

    /**
     * @depends testAddTranslation
     * @param Translate $translate
     */
    public function testSetLocale(Translate $translate)
    {
        $this->assertInstanceOf(Translate::class, $translate->setLocale('pl'));
        $this->assertInstanceOf(Translate::class, $translate->addTranslationFile(BASE_PATH . '/tests/data/en.ini', 'en'));
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

        $this->assertEquals('translation with a sample text string and another text int 13 and %unused% sample text', $translate->translate('test', [
            'firstText' => 'sample text',
            'secondText' => 'another text',
            'number' => 13
        ]));
    }

}
