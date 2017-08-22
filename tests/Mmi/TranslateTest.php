<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test;

use Mmi\Translate;

/**
 * Test obiektu tłumaczeń
 */
class TranslateTest extends \PHPUnit\Framework\TestCase
{

    public function testAddTranslation()
    {
        $translate = new Translate();
        $this->assertInstanceOf('\Mmi\Translate', $translate->addTranslation(BASE_PATH . '/tests/data/pl.ini', 'pl'));
        $this->assertInstanceOf('\Mmi\Translate', $translate->addTranslation(BASE_PATH . '/tests/data/de.ini', 'de'));
        $this->assertInstanceOf('\Mmi\Translate', $translate->addTranslation('/not-important', ''));
        $this->assertInstanceOf('\Mmi\Translate', $translate->addTranslation(BASE_PATH . '/tests/data/pl-extended.ini', 'pl'));
        return $translate;
    }

    /**
     * @depends testAddTranslation
     * @param Translate $translate
     */
    public function testSetLocale(Translate $translate)
    {
        $this->assertInstanceOf('\Mmi\Translate', $translate->setDefaultLocale('en')
                ->setLocale('pl'));
        $this->assertEquals('en', $translate->getDefaultLocale());
        $this->assertInstanceOf('\Mmi\Translate', $translate->addTranslation(BASE_PATH . '/tests/data/en.ini', 'en'));
        $this->assertEquals('pl', $translate->getLocale());
    }
    
    /**
     * @depends testAddTranslation
     * @expectedException \Mmi\App\KernelException
     */
    public function testTranslate(Translate $translate)
    {
        $this->assertEquals('chłopiec', $translate->_('boy'));
        $this->assertEquals('dziewczyna', $translate->_('girl'));
        $this->assertEquals('żółw', $translate->_('turtle'));
        $this->assertEquals('cow', $translate->_('cow'), 'Inexistent translation should return default value');
        
        $this->assertInstanceOf('\Mmi\Translate', $translate->setLocale('de'));
        $this->assertEquals('Junge', $translate->_('boy'));
        $this->assertEquals('girl', $translate->_('girl'));
        $this->assertEquals('Kuh', $translate->_('cow'));

        $this->assertInstanceOf('\Mmi\Translate', $translate->setLocale('en'));
        $this->assertEquals('boy', $translate->_('boy'));
        $this->assertEquals('girl', $translate->_('girl'));
        $this->assertEquals('cow', $translate->_('cow'));
        //wyjątek
        $translate->addTranslation(BASE_PATH . '/tests/data/inexistent-language.ini', 'pl');
    }

}
