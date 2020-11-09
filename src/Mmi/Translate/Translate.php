<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Translate;

/**
 * Obiekt tłumaczeń
 */
class Translate implements TranslateInterface
{

    /**
     * Dane językowe
     * @var array
     */
    private $_data = [];

    /**
     * Bieżąca wersja językowa
     * @var string
     */
    private $_locale;

    /**
     * Adds translation file
     */
    public function addTranslationFile(string $sourceFile, string $locale): self
    {
        //jeśli brak locale
        if (!$locale) {
            //zwraca siebie
            return $this;
        }
        //parser pliku tłumaczeń
        $data = $this->_parseTranslationFile($sourceFile);
        //istnieje tłumaczenie
        $this->_data[$locale] = isset($this->_data[$locale]) ? array_merge($data, $this->_data[$locale]) : $data;
        return $this;
    }

    /**
     * Gets locale
     */
    public function getLocale(): string
    {
        return $this->_locale;
    }

    /**
     * Sets locale
     */
    public function setLocale($locale): self
    {
        //ustawia locale
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Translate string using sprintf notation
     * ->translate('number %d', [12]) returns "number 12"
     */
    public function translate($key, array $params = []): string
    {
        //jeśli brak locale - zwrot klucza
        if (null === $this->_locale) {
            return $key;
        }
        //parametry istnieją
        if (!empty($params)) {
            $filteredParams = [];
            array_walk($params, function ($value, $key) use (&$filteredParams) {
                $filteredParams['%' . $key . '%'] = $value;
            });
            $params = $filteredParams;
        }
        //zwrot znalezionego tłumaczenia
        if (isset($this->_data[$this->_locale][$key])) {
            return strtr($this->_data[$this->_locale][$key], $params);
        }
        //key return
        return strtr($key, $params);
    }

    /**
     * Parsuje plik z tłumaczeniem
     * @param string $sourceFile plik źródłowy
     * @return array
     */
    private function _parseTranslationFile($sourceFile)
    {
        //wczytanie pliku
        $data = explode("\n", str_replace("\r\n", "\n", file_get_contents($sourceFile)));
        $output = [];
        //parsowanie linii
        foreach ($data as $line) {
            //pusta linia
            if (!strlen($line)) {
                continue;
            }
            //parsowanie linii
            $line = explode(" = ", $line);
            //wyznaczanie klucza
            $key = trim($line[0]);
            //tłumaczenie
            $output[$key] = isset($line[1]) ? trim($line[1]) : null;
        }
        //zwrot tablicy tłumaczeń
        return $output;
    }

}
