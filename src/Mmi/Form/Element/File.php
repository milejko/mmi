<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Element plikowy
 */
class File extends ElementAbstract
{

    //szablon pola
    const TEMPLATE_FIELD = 'mmi/form/element/file';

    /**
     * Informacje o zuploadowanym pliku
     * @var \Mmi\Http\RequestFile[]
     */
    private $_files = [];

    /**
     * Ustawia formularz
     * @param \Mmi\Form\Form $form
     * @return \Mmi\Form\Element\File
     */
    public function setForm(\Mmi\Form\Form $form)
    {
        parent::setForm($form);
        //nazwa pola
        $namespace = $form->getBaseName();
        $fieldName = $this->getName();
        //brak załadowanych plików
        if (\Mmi\App\FrontController::getInstance()->getRequest()->getFiles()->isEmpty()) {
            return $this;
        }
        $files = \Mmi\App\FrontController::getInstance()->getRequest()->getFiles()->getAsArray();
        //brak pliku
        if (!isset($files[$namespace]) || !isset($files[$namespace][$fieldName])) {
            return $this;
        }
        $this->_files = $files[$namespace][$fieldName];
        return $this;
    }

    /**
     * Pobiera informacje o wgranym pliku (jeśli istnieje)
     * @return \Mmi\Http\RequestFile[]
     */
    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * Zwraca czy plik został zuploadowany do tego pola
     * @return boolean
     */
    public function isUploaded()
    {
        return !empty($this->_files);
    }

    /**
     * Waliduje pole
     * @return boolean
     */
    public function isValid()
    {
        $result = true;
        //waliduje poprawnie jeśli niewymagane, ale tylko gdy niepuste
        if (!($this->getRequired() || empty($this->getFiles()))) {
            return $result;
        }
        //iteracja po walidatorach
        foreach ($this->getValidators() as $validator) {
            if ($validator->isValid($this->getFiles())) {
                continue;
            }
            $result = false;
            //dodawanie wiadomości z walidatora
            $this->addError($validator->getMessage() ? $validator->getMessage() : $validator->getError());
        }
        //zwrot rezultatu wszystkich walidacji (iloczyn)
        return $result;
    }
}
