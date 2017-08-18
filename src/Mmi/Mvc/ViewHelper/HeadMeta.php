<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class HeadMeta extends HeadAbstract
{

    /**
     * Dane
     * @var array
     */
    private $_data = [];

    /**
     * Metoda główna, dodaje lub nadpisuje właściwość
     * @param array $params parametry opisujące pola
     * @param boolean $replace nadpisz definicję dla klucza
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadMeta
     */
    public function headMeta(array $params = [], $replace = false, $conditional = '')
    {
        //jeśli brak parametrów - wyjście
        if (empty($params)) {
            return $this;
        }
        //warunek
        $params['conditional'] = $conditional;
        if (!array_key_exists(reset($params), $this->_data) || $replace) {
            //wstawienie pod danym kluczem
            $this->_data[reset($params)] = $params;
        }
        return '';
    }

    /**
     * Dodaje znacznik dla Open Graph
     * @param string $property nazwa właściwości, np. og:image
     * @param string $content zawartość
     * @param boolean $replace nadpisz definicję dla klucza
     * @param string $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadMeta
     */
    public function openGraph($property, $content, $replace = false, $conditional = '')
    {
        return $this->headMeta(['property' => $property, 'content' => $content], $replace, $conditional);
    }

    /**
     * Renderer znaczników meta
     * @return string
     */
    public function __toString()
    {
        $html = '';
        foreach ($this->_data as $meta) {
            $conditional = $meta['conditional'];
            unset($meta['conditional']);
            if ($conditional) {
                $html .= '<!--[if ' . $conditional . ']>';
            }
            $html .= '	<meta ';
            foreach ($meta as $key => $value) {
                $html .= $key . '="' . $value . '" ';
            }
            $html .= '/>';
            if ($conditional) {
                $html .= '<![endif]-->';
            }
            $html .= PHP_EOL;
        }
        return $html;
    }

}
