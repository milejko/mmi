<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class HeadLink extends HeadAbstract
{

    /**
     * Dane
     * @var array
     */
    private $_data = [];

    /**
     * Metoda główna, dodająca link do stosu
     * @param array $params parametry linku (jak rel, type, href)
     * @param boolean $prepend dodaj na początek stosu
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function headLink(array $params = [], $prepend = false, $conditional = '')
    {
        //brak parametrów - render
        if (empty($params)) {
            return $this;
        }
        //dokładanie warunku
        $params['conditional'] = $conditional;
        //link już dodany
        if (array_search($params, $this->_data) !== false) {
            return '';
        }
        //wybór prepend / append
        $prepend ? array_unshift($this->_data, $params) : array_push($this->_data, $params);
        return '';
    }

    /**
     * Renderer linków
     * @return string
     */
    public function __toString()
    {
        //pusty html
        $html = '';
        //iteracja po danych
        foreach ($this->_data as $link) {
            $conditional = $link['conditional'];
            unset($link['conditional']);
            //początek conditional
            $html .= $conditional ? '<!--[if ' . $conditional . ']>' : '';
            //rozpoczęcie linku
            $html .= '	<link ';
            //timestamp
            $ts = isset($link['ts']) ? $link['ts'] : 0;
            unset($link['ts']);
            //iteracja po linkach
            foreach ($link as $key => $value) {
                //klucz to href
                if ($key == 'href' && $ts != 0) {
                    //jeśli znaleziono ? - dokładanie &
                    $value .= strpos($value, '?') ? '&ts=' . $ts : '?ts=' . $ts;
                }
                //sklejanie html
                $html .= $key . '="' . $value . '" ';
            }
            //zakończenie linku
            $html .= '/>';
            //zakończenie conditional
            $html .= $conditional ? '<![endif]-->' : '';
        }
        //zwrot html
        return $html;
    }

    /**
     * Dodaje styl CSS na koniec stosu
     * @param string $href adres
     * @param string $media media
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function appendStylesheet($href, $media = null, $conditional = '')
    {
        return $this->_setStylesheet($href, $media, false, $conditional);
    }

    /**
     * Dodaje styl CSS na początek stosu
     * @param string $href adres
     * @param string $media media
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function prependStylesheet($href, $media = null)
    {
        return $this->_setStylesheet($href, $media, true);
    }

    /**
     * Dodaje alternate na koniec stosu
     * @param string $href adres
     * @param string $type typ
     * @param string $title tytuł
     * @param string $media media
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function appendAlternate($href, $type, $title, $media = null, $conditional = '')
    {
        return $this->_setAlternate($href, $type, $title, $media, true, $conditional);
    }

    /**
     * Dodaje alternate na początek stosu
     * @param string $href adres
     * @param string $type typ
     * @param string $title tytuł
     * @param string $media media
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function prependAlternate($href, $type, $title, $media = null, $conditional = '')
    {
        return $this->_setAlternate($href, $type, $title, $media, false, $conditional);
    }

    /**
     * Dodaje canonical na koniec stosu
     * @param string $href adres
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function appendCanonical($href)
    {
        return $this->_setCanonical($href, true);
    }

    /**
     * Dodaje canonical na początek stosu
     * @param string $href adres
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    public function prependCanonical($href)
    {
        return $this->_setCanonical($href, false);
    }

    /**
     * Dodaje styl CSS do stosu
     * @param string $href adres
     * @param string $media media
     * @param boolean $prepend dodaj na początku stosu
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    protected function _setStylesheet($href, $media = null, $prepend = false, $conditional = '')
    {
        //obliczanie timestampa
        $ts = $this->_getLocationTimestamp($href);
        //określanie parametrów
        $params = ['rel' => 'stylesheet', 'type' => 'text/css', 'href' => $ts > 0 ? $this->_getPublicSrc($href) : $href, 'ts' => $ts];
        if ($media) {
            $params['media'] = $media;
        }
        return $this->headLink($params, $prepend, $conditional);
    }

    /**
     * Dodaje canonical do stosu
     * @param string $href adres
     * @param boolean $prepend dodaj na początku stosu
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    protected function _setCanonical($href, $prepend = false)
    {
        return $this->headLink(['rel' => 'canonical', 'href' => $href], $prepend);
    }

    /**
     * Dodaje alternate do stosu
     * @param string $href adres
     * @param string $type typ
     * @param string $title tytuł
     * @param string $media media
     * @param boolean $prepend dodaj na początku stosu
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadLink
     */
    protected function _setAlternate($href, $type, $title, $media = null, $prepend = false, $conditional = '')
    {
        //określanie parametrów
        $params = ['rel' => 'alternate', 'type' => $type, 'title' => $title, 'href' => $href];
        if ($media) {
            $params['media'] = $media;
        }
        return $this->headLink($params, $prepend, $conditional);
    }

}
