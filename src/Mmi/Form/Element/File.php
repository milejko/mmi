<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Form\Element;

use Mmi\App\App;
use Mmi\Http\Request;

/**
 * Element plikowy
 */
class File extends ElementAbstract
{
    //szablon pola
    public const TEMPLATE_FIELD = 'mmi/form/element/file';

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
        //no value
        $this->setIgnore();
        $fieldName = str_replace(['[', ']'], '', $this->getName());
        $fieldNameWithSquareBracket = $fieldName.'[]';
        //brak załadowanych plików
        if (App::$di->get(Request::class)->getFiles()->isEmpty()) {
            return $this;
        }
        $files = App::$di->get(Request::class)->getFiles()->getAsArray();
        //brak pliku
        if (!isset($files[$namespace])) {
            return $this;
        }

        if (true === array_key_exists($fieldName, $files[$namespace])) {
            $this->_files = $files[$namespace][$fieldName];
        }

        if (true === array_key_exists($fieldNameWithSquareBracket, $files[$namespace])) {
            $this->_files = $files[$namespace][$fieldNameWithSquareBracket];
        }

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
        if (false === $this->getRequired() && true === empty($this->getFiles())) {
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
