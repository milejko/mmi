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
 * Klasa pliku
 * 
 * @property string $name nazwa pliku
 * @property string $tmpName tymczasowa ścieżka
 * @property integer $size rozmiar pliku
 * @property string $type mime type
 */
class RequestFile extends \Mmi\DataObject
{

    /**
     * Konstruktor
     * @throws \Mmi\Http\HttpException
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        //brak nazwy
        if (!isset($data['name'])) {
            throw new HttpException('RequestFile: name not specified');
        }
        //brak tmp_name
        if (!isset($data['tmp_name'])) {
            throw new HttpException('RequestFile: tmp_name not specified');
        }
        //brak rozmiaru i samego pliku
        if (!isset($data['size']) && !file_exists($data['tmp_name'])) {
            throw new HttpException('RequestFile: file not found');
        }
        parent::__construct([
            'name' => $data['name'],
            'type' => \Mmi\FileSystem::mimeType($data['tmp_name']),
            'tmpName' => $data['tmp_name'],
            'size' => isset($data['size']) ? $data['size'] : filesize($data['tmp_name'])
        ]);
    }

}
