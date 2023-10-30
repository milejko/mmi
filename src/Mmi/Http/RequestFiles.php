<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

use Mmi\Form\Element\File;

/**
 * Klasa plików
 * @method File[] toArray() Zwraca tablicę obiektów plików \Mmi\Http\RequestFile
 */
class RequestFiles
{
    public const FILE_NAME_KEY = 'name';
    public const FILE_PATH_KEY = 'tmp_name';

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
     * @param array $fieldFiles
     * @return RequestFile[]|null
     * @throws HttpException
     */
    private function _handleFieldFiles(array $fieldFiles)
    {
        //w polu znajduje się jeden plik
        if (null !== ($file = $this->_handleSingleUpload($fieldFiles))) {
            return [$file];
        }
//        var_dump($this->_pivot($fieldFiles));
//        var_dump($this->_handleMultiUpload($this->_pivot($fieldFiles)));
//        var_dump($this->_handleMultiUpload2($this->_pivot2($fieldFiles)));
//        exit;
        //w polu znajduje się kilka plików (dołączanie plików)
        return $this->_handleMultiUpload($this->_pivot($fieldFiles));
    }

    /**
     * Obsługa formularza z róznymi typami pól: typu files, user[files], lub user[file][]
     * @param array $data
     * @param array $node
     * @throws HttpException
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
            //pusta ściezka, lub brak nazwy (uszkodzony plik)
            if ('' == $expectedFieldFiles[self::FILE_PATH_KEY] || !isset($expectedFieldFiles[self::FILE_NAME_KEY])) {
                continue;
            }
            $node[$nodeName] = $this->_handleFieldFiles($expectedFieldFiles);
        }
    }

    /**
     * Obsługa pojedynczego uploadu
     * @param array $fileData dane pliku
     * @return RequestFile|null
     * @throws HttpException
     */
    private function _handleSingleUpload(array $fileData): ?RequestFile
    {
        //brak ścieżki pliku
        if (!isset($fileData[self::FILE_PATH_KEY]) || '' === $fileData[self::FILE_PATH_KEY]) {
            return null;
        }
        //multiupload (pomijany w tej metodzie)
        if (is_array($fileData[self::FILE_NAME_KEY])) {
            return null;
        }
        //tworzenie obiektu plików na tablicy z danymi
        return new RequestFile($fileData);
    }

    /**
     * Obsługa uploadu wielu plików (HTML5)
     * @param array $fieldFiles dane plików
     * @return RequestFile[]
     * @throws HttpException
     */
    private function _handleMultiUpload(array $fieldFiles)
    {
        $files = [];
        //iteracja po plikach
        foreach ($fieldFiles as $fileName => $fileData) {
            //plik uszkodzony, lub brak ściezki pliku
            if (isset($fileData[self::FILE_PATH_KEY]) && '' !== $fileData[self::FILE_PATH_KEY]) {
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
                if (isset($singleFile[self::FILE_PATH_KEY]) && '' !== $singleFile[self::FILE_PATH_KEY]) {
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
     * Obsługa uploadu wielu plików (HTML5)
     * @param array $fieldFiles dane plików
     * @return RequestFile[]|null
     * @throws HttpException
     */
    private function _handleMultiUpload2(array $fieldFiles): ?array
    {
        $files = [];
        foreach ($fieldFiles as $fileName => $fileData) {
            if (!is_array($fileData)) {
                return null;
            }
            if ($file = $this->_handleSingleUpload($fileData) ?? $this->_handleMultiUpload2($fileData)) {
                $files[$fileName] = $file;
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
                    if (!is_array($scalarPropertyValue)) {
                        $pivot[$fieldName][$index][$fileProperty] = $scalarPropertyValue;
                        continue;
                    }
                    foreach ($scalarPropertyValue as $index2 => $scalarPropertyValue2) {
                        $pivot[$fieldName . '-' . $index2][$index][$fileProperty] = $scalarPropertyValue2;
                    }
                }
            }
        }
        return $pivot;
    }

    /**
     * Zmienia tabelę z postaci $_FILES na przyjazną
     * @param array $fieldFiles
     * @return array
     */
    private function _pivot2(array $fieldFiles)
    {
        $pivot = [];
        //iteracja po plikach
        foreach ($fieldFiles as $fileProperty => $propertyValues) {
            //file invalid
            if (!is_array($propertyValues)) {
                continue;
            }
            //iteracja po polach
            $this->_completePivot($fileProperty, $propertyValues, $pivot);
        }
        return $pivot;
    }

    /**
     * Kompletuje tablice z danymi do grafik
     * @param string $fileProperty
     * @param array $propertyValues
     * @param $pivot
     * @return void
     */
    private function _completePivot(string $fileProperty, array $propertyValues, &$pivot): void
    {
        foreach ($propertyValues as $key => $value) {
            if (is_array($value)) {
                $this->_completePivot($fileProperty, $value, $pivot[$key]);
                continue;
            }
            $pivot[$key][$fileProperty] = $value;
        }
    }
}
