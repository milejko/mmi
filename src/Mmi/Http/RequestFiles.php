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
class RequestFiles
{

    const FILE_NAME_KEY = 'name';
    const FILE_PATH_KEY = 'tmp_name';

    /**
     * Tablica zawierająca strukturę plików
     */
    protected $_files = [];

    /**
     * Konstruktor
     * @param array $data dane z FILES
     */
    public function __construct(array $data = [])
    {
        //obsługa uploadu plików
        $this->_handleForm($data, $this->_files);
    }

    /**
     * Zwraca tablicę z plikami
     * @return RequestFiles[]
     */
    public function getAsArray()
    {
        return $this->_files;
    }

    /**
     * Sprawdza pustość requestu
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->_files);
    }

    /**
     * Obsługa prostego formularza (pola z nazwami string)
     * @param array $fileData
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
     * Obsługa formularza z róznymi typami pól: typu files, user[files], lub user[file][]
     * @param array $data
     * @return array
     */
    private function _handleForm(array $data, array &$node)
    {
        foreach ($data as $nodeName => $expectedFieldFiles) {
            //brak tablicy
            if (!is_array($expectedFieldFiles)) {
                continue;
            }
            $node[$nodeName] = [];
            //kolejne zagłębienie w tablicy -> zejście rekurencyjne
            if (!isset($expectedFieldFiles[self::FILE_PATH_KEY])) {
                $this->_handleForm($expectedFieldFiles, $node[$nodeName]);
                continue;
            }
            //pusta ściezka, lub brak nazwy (uszkodzony plik)
            if ('' == $expectedFieldFiles[self::FILE_PATH_KEY] || !isset($expectedFieldFiles[self::FILE_NAME_KEY])) {
                continue;
            }
            $node[$nodeName] = $this->_handleFieldFiles($expectedFieldFiles);
        }
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
     * @return RequestFile[]
     */
    private function _handleMultiUpload(array $fieldFiles)
    {
        $files = [];
        //iteracja po plikach
        foreach ($this->_pivot($fieldFiles) as $fileName => $fileData) {
            //plik uszkodzony, lub brak ściezki pliku
            if (isset($fileData[self::FILE_PATH_KEY]) && '' != $fileData[self::FILE_PATH_KEY]) {
                //dodawanie pliku do tabeli
                is_int($fileName) ?
                    $files[$fileName] = new RequestFile($fileData) : $files[$fileName][] = new RequestFile($fileData);
                continue;
            }
            if (!isset($files[$fileName])) {
                $files[$fileName] = [];
            }
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
