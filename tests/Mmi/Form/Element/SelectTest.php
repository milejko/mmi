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
        $this->assertEquals('single', $element->getName());
        $this->assertEquals('single', $element->getBaseName());
        $this->assertEquals('<select name="single" data-requiredAsterisk="*" data-labelPostfix=":" class="field" ><option value hidden disabled selected>form.select.choose.label</option><option value="foo">bar</option><option value="baz">baz</option></select>', $element->fetchField());
    }

    public function testIfMultiselectIsProperlyRendered(): void
    {
        $element = (new Select('multi'))
            ->setMultiple()
            ->setMultioptions(['foo' => 'bar', 'baz' => 'baz']);
        $this->assertEquals('multi', $element->getName());
        $this->assertEquals('multi', $element->getBaseName());
        $this->assertEquals('<select name="multi[]" data-requiredAsterisk="*" data-labelPostfix=":" class="field" multiple="" ><option value hidden disabled selected>form.select.choose.label</option><option value="foo">bar</option><option value="baz">baz</option></select>', $element->fetchField());
    }
}
