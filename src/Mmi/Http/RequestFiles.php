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

    /**
     * Konstruktor
     * @param array $data dane z FILES
     */
    public function __construct(array $data = [])
    {
        //obsługa uploadu plików
        parent::__construct($this->_handleUpload($data));
    }

    /**
     * Zwraca tablicę obiektów plików
     * @param array $data
     * @return array
     */
    protected function _handleUpload(array $data)
    {
        $files = [];
        //iteracja po tablicy plików
        foreach ($data as $fieldName => $fieldFiles) {
            //brak pliku o podanej nazwie
            if (!isset($files[$fieldName])) {
                $files[$fieldName] = [];
            }
            //pojedynczy plik
            if (null !== ($file = $this->_handleSingleUpload($fieldFiles))) {
                $files[$fieldName] = $file;
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
        //wstepne sprawdzenie czy plik istnieje, czy tablica nie jest pusta
        if(!isset($fileData['name']) || !isset($fileData['tmp_name'])){
            return;
        }

        //przypadek gdy file data to prosta tablica klucz => wartość bez zagnieżdżeń
        // $fileData = ['name' => 'nazwa_pliku.jpg']
        if(is_string($fileData['name'])){
            //czy plik istnieje
            if($fileData['tmp_name'] == ''){
                return;
            }
            $fieldName = $fileData['name'];
        }

        //przypadek trochę bardziej skomplikowany gdzie sa zagnieżdżenia:
        // $fileData['name'] = ['cmsAttribute' => 'nazwa_pliku.png']
        if(is_array($fileData['name'])){
            //sprawdzamy czy nie ejst to kilka plików
            if(count(reset($fileData['name'])) > 1){
                return;
            }
            //czy plik istnieje
            if(is_array(reset($fileData['name'])) || reset($fileData['tmp_name']) == ''){
                return;
            }

            $fieldName = key($fileData['name']);

            foreach ($fileData as $key => $value) {
                $fileData[$key] = current($value);
            }
        }

        $file = new RequestFiles();
        $file->{$fieldName} = new RequestFile($fileData);

        return  $file;
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
        $fixFiles = $this->_fixFiles($fileData);
        foreach ($fixFiles as $key => $file) {
            //brak pliku
            if (!isset($file['tmp_name']) || $file['tmp_name'] == '') {
                continue;
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
            if (!is_array($all)) {
                break;
            }
            foreach ($all as $fieldName => $val) {
                for ($i = 0; $i < count($val); $i++){
                    $fixed[$fieldName . '[' . $i . ']'][$key] = $val[$i];
                }
            }
        }
        return $fixed;
    }
}
