<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Form\Element;

use Mmi\Form\Element\Select;

/**
 * Test obiektu select
 */
class SelectTest extends \PHPUnit\Framework\TestCase
{
    public function testIfSingleSelectIsProperlyRendered(): void
    {
        $element = (new Select('single'))
            ->setMultioptions(['foo' => 'bar', 'baz' => 'baz']);
        self::assertEquals('single', $element->getName());
        self::assertEquals('single', $element->getBaseName());        
        self::assertEquals('<select name="single" data-requiredAsterisk="*" data-labelPostfix=":" class="field" ><option value="foo">bar</option><option value="baz">baz</option></select>', $element->fetchField());
    }


    public function testIfMultiselectIsProperlyRendered(): void
    {
        $element = (new Select('multi'))
            ->setMultiple()
            ->setMultioptions(['foo' => 'bar', 'baz' => 'baz']);
        self::assertEquals('multi', $element->getName());
        self::assertEquals('multi', $element->getBaseName());        
        self::assertEquals('<select name="multi[]" data-requiredAsterisk="*" data-labelPostfix=":" class="field" multiple="" ><option value="foo">bar</option><option value="baz">baz</option></select>', $element->fetchField());
    }
}