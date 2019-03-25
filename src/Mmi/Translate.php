<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi;

/**
 * Obiekt tłumaczeń
 */
class Translate
{

    /**
     * Dane językowe
     * @var array
     */
    private $_data = [];

    /**
     * Dostępne języki
     * @var array
     */
    private $_languages = [];

    /**
     * Bieżąca wersja językowa
     * @var string
     */
    private $_locale;

    /**
     * Dodaje tłumaczenie
     * @param string $sourceFile ścieżka do pliku
     * @param string $locale wersja językowa podanego pliku
     * @return \Mmi\Translate
     */
    public function addTranslation($sourceFile, $locale)
    {
        //jeśli brak locale
        if (!$locale) {
            //zwraca siebie
            return $this;
        }
        //parser pliku tłumaczeń
        $data = $this->_parseTranslationFile($sourceFile);
        //dodawanie języka
        $this->_languages[$locale] = $sourceFile;
        //istnieje tłumaczenie
        if (isset($this->_data[$locale])) {
            //łączenie tłumaczeń
            $data = array_merge($this->_data[$locale], $data);
        }
        //dodanie tłumaczenia
        $this->_data[$locale] = $data;
        return $this;
    }

    /**
     * Pobiera bieżącą wersję językową
     * @return string
     */
    public function getLocale()
    {
        return $this->_locale;
    }

    /**
     * Ustawia bieżącą wersję językową
     * @param string $locale wersja językowa
     * @return \Mmi\Translate
     */
    public function setLocale($locale)
    {
        //ustawia locale
        $this->_locale = $locale;
        return $this;
    }

    /**
     * Tłumaczy ciąg znaków, działając analogicznie do sprintf
     * przykład : :translate('number %d', 12) wyświetli np. "liczba 12"
     * @return string
     */
    public function _($key, array $params = [])
    {
        //jeśli brak locale - zwrot klucza
        if ($this->_locale === null) {
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
        //logowanie braku tłumaczenia i zwrot klucza
        return $this->_returnKeyAndLogUntranslated($key, $params);
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

    /**
     * Loguje nieprzetłumaczone teksty do pliku
     * @param string $key klucz
     */
    private function _returnKeyAndLogUntranslated($key, array $params)
    {
        //debug log
        \Mmi\App\FrontController::getInstance()->getLogger()->debug('Translate: ' . \Mmi\App\FrontController::getInstance()->getEnvironment()->requestUri . ' [' . $this->_locale . '] ' . $key);
        //zwrot klucza
        return $key;
    }
}
