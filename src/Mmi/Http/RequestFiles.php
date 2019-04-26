<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

/**
 * Klasa plików
 * @method File[] toArray() Zwraca tablicę obiektów plików \Mmi\Http\RequestFile
 */
class RequestFiles extends \Mmi\DataObject
{

    const FILE_NAME_KEY = 'name';
    const FILE_PATH_KEY = 'tmp_name';

    /**
     * Konstruktor
     * @param array $data dane z FILES
     */
    public function __construct(array $data = [])
    {
        //obsługa uploadu plików
        parent::__construct($this->_handleNestedForm($data));
    }

    /**
     * Obsługa prostego formularza (pola z nazwami string)
     * @param array $fileData
     * @return array
     */
    private function _handleFieldFiles(array $fieldFiles)
    {
        //w polu znajduje się jeden plik
        if (null !== ($file = $this->_handleSingleUpload($fieldFiles))) {
            return [$file];
        }
        //w polu znajduje się kilka plików (dołączanie plików)
        return $this->_handleMultiUpload($fieldFiles);
    }

    /**
     * Obsługa formularza z polami tablicowymi: typu user[files], lub user[file][]
     * @param array $data
     * @return array
     */
    private function _handleNestedForm(array $data)
    {
        $files = [];
        foreach ($data as $expectedFieldName => $expectedFieldFiles) {
            //brak tablicy
            if (!is_array($expectedFieldFiles)) {
                continue;
            }
            //kolejne zagłębienie w tablicy -> zejście rekurencyjne
            if (!isset($expectedFieldFiles[self::FILE_PATH_KEY])) {
                $this->_handleNestedForm($expectedFieldFiles);
                continue;
            }
            //pusta ściezka, lub brak nazwy (uszkodzony plik)
            if ('' == $expectedFieldFiles[self::FILE_PATH_KEY] || !isset($expectedFieldFiles[self::FILE_NAME_KEY])) {
                continue;
            }
            $files[$expectedFieldName] = $this->_handleFieldFiles($expectedFieldFiles);
        }
        return $files;
    }

    /**
     * Obsługa pojedynczego uploadu
     * @param array $fileData dane pliku
     * @return \Mmi\Http\RequestFile
     */
    private function _handleSingleUpload(array $fileData)
    {
        //multiupload (pomijany w tej metodzie)
        if (is_array($fileData[self::FILE_NAME_KEY])) {
            return;
        }
        //brak ściezki pliku
        if ('' == $fileData[self::FILE_PATH_KEY]) {
            return;
        }
        //tworzenie obiektu plików na tablicy z danymi
        return new RequestFile($fileData);
    }

    /**
     * Obsługa uploadu wielu plików (HTML5)
     * @param array $fieldFiles dane plików
     * @return \Mmi\Http\RequestFile[]
     */
    private function _handleMultiUpload(array $fieldFiles)
    {
        $files = [];
        //iteracja po plikach
        foreach ($this->_pivot($fieldFiles) as $fileName => $fileData) {
            //plik uszkodzony, lub brak ściezki pliku
            if (isset($fileData[self::FILE_PATH_KEY]) && '' != $fileData[self::FILE_PATH_KEY]) {
                //dodawanie pliku do tabeli
                $files[$fileName] = new RequestFile($fileData);
                continue;
            }
            $files[$fileName] = [];
            //iterate multiple files in multiupload
            foreach ($fileData as $singleFile) {
                if (isset($singleFile[self::FILE_PATH_KEY]) && '' != $singleFile[self::FILE_PATH_KEY]) {
                    //dodawanie pliku do tabeli
                    $files[$fileName][] = new RequestFile($singleFile);
                }
            }
            //empty field
            if (empty($files[$fileName])) {
                unset($files[$fileName]);
            }
        }
        return $files;
    }

    /**
     * Zmienia tabelę z postaci $_FILES na przyjazną
     * @param array $fieldFiles
     * @return array
     */
    private function _pivot(array $fieldFiles)
    {
        $pivot = [];
        //iteracja po plikach
        foreach ($fieldFiles as $fileProperty => $propertyValues) {
            //file invalid
            if (!is_array($propertyValues)) {
                continue;
            }
            //iteracja po polach
            foreach ($propertyValues as $fieldName => $propertyValue) {
                //wartość skalarna (jeden plik w multiuploaderze)
                if (!is_array($propertyValue)) {
                    $pivot[$fieldName][$fileProperty] = $propertyValue;
                    continue;
                }
                //wiele plików w multiuploaderze
                foreach ($propertyValue as $index => $scalarPropertyValue) {
                    $pivot[$fieldName][$index][$fileProperty] = $scalarPropertyValue;
                }
            }
        }
        return $pivot;
    }
}
