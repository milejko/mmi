<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test;

use Mmi\Form;

/**
 * Test obiektu danych
 */
class FormTest extends \PHPUnit\Framework\TestCase
{

    public function testNew()
    {
        require 'SampleForm.php';
        $form = new SampleForm();
        foreach ($form->getElements() as $name => $element) {
            $this->assertEquals('l:' . $name, $element->getLabel());
            $this->assertEquals('d:' . $name, $element->getDescription());
            $this->assertEquals('v:' . $name, $element->getValue());
            $this->assertCount(2, $element->getFilters());
            $this->assertGreaterThanOrEqual(1, $element->getValidators());
        }
    }

}
