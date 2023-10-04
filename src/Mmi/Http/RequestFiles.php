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
     * @return array
     * @throws HttpException
     */
    private function _handleFieldFiles(array $fieldFiles): array
    {
        //w polu znajduje się jeden plik
        if (null !== ($file = $this->_handleSingleUpload($fieldFiles))) {
            return [$file];
        }
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
    private function _handleMultiUpload(array $fieldFiles): array
    {
        $files = [];
        //iteracja po plikach
        foreach ($fieldFiles as $fileName => $fileData) {
            if (!is_array($fileData)) {
                continue;
            }
            if ($file = $this->_handleSingleUpload($fileData) ?? $this->_handleMultiUpload($fileData)) {
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
