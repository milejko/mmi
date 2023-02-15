<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Form;

use Mmi\Form\Element\Button;
use Mmi\Form\Element\Checkbox;
use Mmi\Form\Element\Csrf;
use Mmi\Form\Element\Email;
use Mmi\Form\Element\File;
use Mmi\Form\Element\Hidden;
use Mmi\Form\Element\Label;
use Mmi\Form\Element\MultiCheckbox;
use Mmi\Form\Element\Password;
use Mmi\Form\Element\Radio;
use Mmi\Form\Element\Select;
use Mmi\Form\Element\Submit;
use Mmi\Form\Element\Text;
use Mmi\Form\Element\Textarea;

/**
 * Test obiektu danych
 */
class SampleForm extends \Mmi\Form\Form
{
    public function init()
    {
        $this->addElement(new Button('button'));

        $this->addElement(new Checkbox('checkbox'));

        $this->addElement(new Csrf('csrf'));

        $this->addElement(new Email('email'));

        $this->addElement(new File('file'));

        $this->addElement(new Hidden('hidden'));

        $this->addElement(new Label('label'));

        $this->addElement((new MultiCheckbox('multicheckbox'))
            ->setMultioptions([null => '', 0 => 'NO', 1 => 'YES']));

        $this->addElement(new Password('password'));

        $this->addElement((new Radio('radio'))
            ->setMultioptions([null => '', 0 => 'NO', 1 => 'YES']));

        $this->addElement((new Select('select'))
            ->setMultioptions([null => '', 0 => 'NO', 1 => 'YES']));

        $this->addElement(new Submit('submit'));

        $this->addElement(new Text('text'));

        $this->addElement(new Textarea('textarea'));

        foreach ($this->getElements() as $name => $element) {
            $element->setLabel('l:' . $name)
                ->setDescription('d:' . $name)
                ->setValue('v:' . $name)
                ->addFilter(new \Mmi\Filter\Capitalize())
                ->addFilter(new \Mmi\Filter\Ascii())
                ->addValidator(new \Mmi\Validator\StringLength([3, 30, 'Invalid string length']));
        }
    }
}
