<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Validator;

use Mmi\Validator\Checked;

class CheckedTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        $checkboxChecked = (new \Mmi\Form\Element\Checkbox('checked'))->setChecked();
        $checkboxUnchecked = (new \Mmi\Form\Element\Checkbox('unchecked'));
        //równe
        $this->assertTrue((new Checked([$checkboxChecked]))->isValid());
        //nie równe
        $this->assertFalse((new Checked([$checkboxUnchecked]))->isValid());
    }

}
