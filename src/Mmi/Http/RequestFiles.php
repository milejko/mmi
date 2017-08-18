<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Http;

/**
 * Klasa plików
 * @method File[] toArray() Zwraca tablicę obiektów plików \Mmi\Http\RequestFile
 */
class RequestFiles extends \Mmi\DataObject
{

    /**
     * Konstruktor
     * @param array $data dane z FILES
     */
    public function __construct(array $data = [])
    {
        //obsługa uploadu plików
        $this->setParams($this->_handleUpload($data));
    }

    /**
     * Zwraca tablicę obiektów plików
     * @param array $data
     * @return array
     */
    protected function _handleUpload(array $data)
    {
        $files = [];
        foreach ($data as $fieldName => $fieldFiles) {
            if (!isset($files[$fieldName])) {
                $files[$fieldName] = [];
            }
            //pojedynczy plik
            if (null !== ($file = $this->_handleSingleUpload($fieldFiles))) {
                $files[$fieldName][] = $file;
                continue;
            }
            //obsługa multiuploadu HTML5
            $files[$fieldName] = $this->_handleMultiUpload($fieldFiles);
        }
        return $files;
    }

    /**
     * Obsługa pojedynczego uploadu
     * @param array $fileData dane pliku
     * @return \Mmi\Http\RequestFile
     */
    protected function _handleSingleUpload(array $fileData)
    {
        //jeśli nazwa jest tablicą, oznacza to wielokrotny upload HTML5
        if (is_array($fileData['name'])) {
            return;
        }
        //brak pliku
        if (!isset($fileData['tmp_name']) || $fileData['tmp_name'] == '') {
            return;
        }
        $fileData['type'] = \Mmi\FileSystem::mimeType($fileData['tmp_name']);
        return new RequestFile($fileData);
    }

    /**
     * Obsługa uploadu wielu plików (HTML5)
     * @param array $fileData dane plików
     * @return \Mmi\Http\RequestFile[]
     */
    protected function _handleMultiUpload(array $fileData)
    {
        $files = new RequestFiles();
        //iteracja po plikach
        foreach ($this->_fixFiles($fileData) as $key => $file) {
            //brak pliku
            if (!isset($file['tmp_name']) || $file['tmp_name'] == '') {
                return;
            }
            //dodawanie pliku
            $files->{$key} = new RequestFile($file);
        }
        return $files;
    }

    /**
     * Zmienia tabelę z postaci $_FILES na przyjazną
     * @param array $files
     * @return array
     */
    protected function _fixFiles(array $files)
    {
        $fixed = [];
        //iteracja po plikach
        foreach ($files as $key => $all) {
            foreach ($all as $i => $val) {
                $fixed[$i][$key] = $val;
            }
        }
        return $fixed;
    }

}
