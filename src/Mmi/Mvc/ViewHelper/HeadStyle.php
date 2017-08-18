<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

/**
 * Helper umieszczający styl w kodzie HTML
 */
class HeadStyle extends HelperAbstract
{

    /**
     * Dane
     * @var array
     */
    private $_data = [];

    /**
     * Metoda główna, dodaje styl do stosu
     * @param array $params
     * @param boolean $prepend
     * @param string $conditional
     * @return \Mmi\Mvc\ViewHelper\HeadStyle
     */
    public function headStyle(array $params = null, $prepend = false, $conditional = '')
    {
        //brak parametrów
        if (!is_array($params)) {
            return $this;
        }
        //warunek
        $params['conditional'] = $conditional;
        //styl jest już dodany
        if (array_search($params, $this->_data) !== false) {
            return '';
        }
        //prepend 
        if ($prepend) {
            array_unshift($this->_data, $params);
        } else {
            //append
            array_push($this->_data, $params);
        }
        return '';
    }

    /**
     * Renderer styli
     * @return string
     */
    public function __toString()
    {
        $html = '';
        //rendering pojedynczych styli
        foreach ($this->_data as $style) {
            //instrukcja warunkowa dla stylu
            if ($style['conditional']) {
                $html .= '<!--[if ' . $style['conditional'] . ']>';
            }
            //nagłówek stylu
            $html .= '<style ';
            //doklejanie atrybutów stylu
            foreach ($style as $key => $value) {
                //pomijanie opcji technicznych
                if (in_array($key, ['style', 'conditional', 'media'])) {
                    continue;
                }
                $html .= $key . '="' . $value . '" ';
            }
            //zamknięcie znacznika style
            $html .= '>';
            //body stylu
            if (isset($style['style'])) {
                $html .= PHP_EOL . '/* <![CDATA[ */' . PHP_EOL . $style['style'] . PHP_EOL . '/* ]]> */';
            }
            //zamknięcie stylu
            $html .= '</style>';
            //zanknięcie warunku wyświetlenia
            if ($style['conditional']) {
                $html .= '<![endif]-->';
            }
        }
        return $html;
    }

    /**
     * Dodaje na koniec stosu treść css
     * @param string $style treść skryptu
     * @param array $params parametry dodatkowe
     * @param boolean $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadStyle
     */
    public function appendStyle($style, array $params = [], $conditional = '')
    {
        return $this->setStyle($style, $params, false, $conditional);
    }

    /**
     * Dodaje na koniec stosu treść pliku css
     * @param string $fileName nazwa pliku ze skryptem
     * @param array $params parametry dodatkowe
     * @param boolean $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadStyle
     */
    public function appendStyleFile($fileName, array $params = [], $conditional = '')
    {
        return $this->appendStyle($this->_getStyleContent($fileName), $params, $conditional);
    }

    /**
     * Dodaje na początek stosu treść pliku css
     * @param string $fileName nazwa pliku ze skryptem
     * @param array $params parametry dodatkowe
     * @param boolean $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadStyle
     */
    public function prependStyleFile($fileName, array $params = [], $conditional = '')
    {
        return $this->appendStyle($this->_getStyleContent($fileName), $params, $conditional);
    }

    /**
     * Dodaje na początek stosu treść css
     * @param string $style treść skryptu
     * @param array $params parametry dodatkowe
     * @param boolean $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadStyle
     */
    public function prependStyle($style, array $params = [], $conditional = '')
    {
        return $this->setStyle($style, $params, true, $conditional);
    }

    /**
     * Dodaje do stosu treść skryptu
     * @param string $style treść skryptu
     * @param array $params parametry dodatkowe
     * @param boolean $prepend dodaj na początek stosu
     * @param boolean $conditional warunek np. ie6
     * @return \Mmi\Mvc\ViewHelper\HeadStyle
     */
    public function setstyle($style, array $params = [], $prepend = false, $conditional = '')
    {
        return $this->headStyle(array_merge($params, ['type' => 'text/css', 'style' => $style]), $prepend, $conditional);
    }

    /**
     * Pobiera zawartość CSS
     * @param string $fileName
     * @return string
     */
    protected function _getStyleContent($fileName)
    {
        //wczytanie stylu z cache
        if (null !== ($cache = $this->view->getCache()) && (null !== ($content = $cache->load($cacheKey = 'Head-Style-Css-' . md5($fileName))))) {
            //zwrot z cache
            return $content;
        }
        //rendering i zapis cache
        $cache->save($content = $this->_filterCssFile($fileName), $cacheKey, 0);
        return $content;
    }

    /**
     * Zwraca wyfiltrowany CSS
     * @param string $fileName
     * @return string
     */
    protected function _filterCssFile($fileName)
    {
        try {
            //pobranie kontentu
            $content = file_get_contents(BASE_PATH . '/web/' . $fileName);
        } catch (\Exception $e) {
            return '/* CSS file not found: ' . $fileName . ' */';
        }
        //lokalizacja zasobów z uwzględnieniem baseUrl
        $location = $this->view->baseUrl . '/' . trim(dirname($fileName), '/') . '/';
        //usuwanie nowych linii i tabów
        return preg_replace(['/\r\n/', '/\n/', '/\t/'], '', str_replace(['url(\'', 'url("'], ['url(\'' . $location, 'url("' . $location], $content));
    }

}
