<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test;

/**
 * Test obiektu danych
 */
class SampleForm extends \Mmi\Form\Form
{

    public function init()
    {
        $this->addElementButton('button');

        $this->addElementCheckbox('checkbox');

        $this->addElementCsrf('csrf');

        $this->addElementEmail('email');

        $this->addElementFile('file');

        $this->addElementHidden('hidden');

        $this->addElementLabel('label');

        $this->addElementMultiCheckbox('multicheckbox')
            ->setMultioptions([null => '', 0 => 'NO', 1 => 'YES']);

        $this->addElementPassword('password');
        
        $this->addElementRadio('radio')
            ->setMultioptions([null => '', 0 => 'NO', 1 => 'YES']);
        
        $this->addElementSelect('select')
            ->setMultioptions([null => '', 0 => 'NO', 1 => 'YES']);
        
        $this->addElementSubmit('submit');
        
        $this->addElementText('text');
        
        $this->addElementTextarea('textarea');
        
        foreach ($this->getElements() as $name => $element) {
            $element->setLabel('l:' . $name)
                ->setDescription('d:' . $name)
                ->setValue('v:' . $name)
                ->addFilterCapitalize()
                ->addFilterAscii()
                ->addValidatorStringLength(3, 30, 'Invalid string length');
        }
        
    }

}
