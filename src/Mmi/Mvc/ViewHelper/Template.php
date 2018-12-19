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
 * Helper kompilacji szablonów
 */
class Template extends HelperAbstract
{

    /**
     * Parsuje kod szablonu do kodu PHP
     * @param string $input kod wejściowy
     * @return string kod PHP
     */
    public function template($input)
    {
        //buforowanie renderowanie szablonu
        $input = preg_replace_callback('/\{\'([a-zA-Z\-\/]+)\'\}/', [&$this, '_render'], $input);

        /**
         * obsługa klamr
         * obsługa zakończeń linii windows
         * komentarzy
         */
        $input = str_replace([
            '{{$',
            '{{',
            '}}',
            "\r\n",
            '{*',
            '*}',
        ], [
            '__-angular-controller-__',
            '__-angular-start-__',
            '__-angular-end-__',
            "\n",
            '<?php /*',
            '*/ ?>',
        ], $input);

        //buforowanie linkowanie aplikacji
        $input = preg_replace_callback('/\{@([\*]+)?(.[^@\^]+)?[\^]?(.[^@\^]+)?@\}/', [&$this, '_url'], $input);

        //zmiana zmiennych obiektowych w linkach np. $request->test
        $input = preg_replace_callback('/%7B((%3E|%28|%29|%24)?([a-zA-Z\.\-\_\[\]\'\"\(\)]+)?)+%7D/', [&$this, '_routerLinks'], $input);

        //zmiana zmiennych skalarnych lub tablicowych w linkach np. $requestTest
        $input = preg_replace('/%7B%24([a-z0-9A-Z\.\-\_\[\]\'\"\(\)]+)%7D/', '{$$1}', $input);

        //buforowanie tłumaczeń
        $input = preg_replace_callback('/\{\#(.[^#]+)#\}/', [&$this, '_translate'], $input);

        //buforowanie wstawień tekstów statycznych
        $input = preg_replace_callback('/\{\=(.[^=]+)\=\}/', [&$this, '_text'], $input);

        //filtry na zmiennych (filtry widoku)
        $input = preg_replace_callback('/(\$[a-z0-9_\[\]\'\"\-\>]+)(\|(.[^}^{^)^(]+))+/i', [&$this, '_filter'], $input);

        //obsługa tagów Mmi
        $input = preg_replace([
            '/\$([a-z0-9_-]+)/i', //$zmienna -> $this->zmienna
            '/\{\/(if|for|foreach|while)\}/', //końcówki struktur językowych
            '/\{(break|continue)\}/', //break continue
            '/\{return\}/', //wyjście
            '/\{(if|elseif|foreach|for|while)([^\}]+)\}/', //struktury językowe
            '/([a-z0-9_-]+)\(/i', //funkcja( -> $this->funkcja(
            '/\$this->(array)\(/', //naprawianie array
            '/\$this->([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)/i', //tabele po kropce
            '/\$this->([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)/i', //tabele po kropce
            '/\$this->([a-zA-Z0-9_-]+)\.([a-zA-Z0-9_-]+)/i', //tabele po kropce
            '/\->\$this->/', //naprawianie metod
            '/\:\:\$this->/', //naprawianie staticów
            '/\{else\}/', //else
            '/\{\$this->([a-z0-9_-]+)([\s]?\=)/i', //przypisanie wartości do zmiennej
            '/\{\$this->([a-z0-9_-]+)([\s]?\+\=|\-\=|\+\+|\-\-|\*\=)/i', //przypisanie wartości do zmiennej
            '/\{\$this->([a-z0-9_-]+)/i', //wyświetlanie pól
            '/\{\$this->php_([a-z0-9_-]+)\(/i', //wyświetlanie funkcji php
            '/\$this->php_([a-z0-9_-]+)\(/i', //zamiana na funkcje php wbudowane
            '/\{\$this->system_([a-z0-9_-]+)\(/i', //wyświetlanie funkcji php
            '/\$this->system_([a-z0-9_-]+)\(/i', //zamiana na funkcje php wbudowane
            '/\{\$this->([a-z0-9_-]+)\(/i', //wyświetlanie helperów
            '/([a-z0-9)\]\'\+\-])}/i', //obsługa zamknięcia
            '/\{\$\$this->([a-z0-9_-]+)/i', //$nazwa -> $this->{$this->nazwa}
        ], [
            '$this->${1}',
            '<?php end${1}; ?>',
            '<?php ${1}; ?>',
            '<?php return; ?>',
            '<?php ${1} (${2}): ?>',
            '$this->${1}(',
            '${1}(',
            '$this->${1}[\'${2}\'][\'${3}\'][\'${4}\']',
            '$this->${1}[\'${2}\'][\'${3}\']',
            '$this->${1}[\'${2}\']',
            '->',
            '::$',
            '<?php else: ?>',
            '<?php \$this->${1} ${2} ',
            '<?php \$this->${1}${2}',
            '<?php echo \$this->${1}',
            '<?php echo ${1}(',
            '${1}(',
            '<?php echo ${1}(',
            '${1}(',
            '<?php echo \$this->${1}(',
            '${1}; ?>',
            '<?php echo \$this->{$this->${1}}',
        ], $input);

        /**
         * first i last w pętlach foreach
         * uwaga: source i count są zmiennymi lokalnymi niewidocznymi z poziomu view
         */
        $input = preg_replace([
            '/\<\?php foreach[\s]+?\([\s]+?name=[\'\"]?([a-z0-9]+)[\'\"]? (\$this[\>\(\)a-z0-9_\-\[\]\'\"]+) as (\$this->[a-z0-9_\-]+)\):/i',
            '/\<\?php foreach[\s]+?\([\s]+?name=[\'\"]?([a-z0-9]+)[\'\"]? (\$this[\>\(\)a-z0-9_\-\[\]\'\"]+) as (\$this->[a-z0-9_\-]+) => (\$this->[a-z0-9_\-]+)\):/i'
        ], [
            '<?php $_${1}Source = ${2}; $_${1}Count = count((array)$_${1}Source); $this->_${1}Index = 0; foreach ( $_${1}Source as ${3}): $this->_${1}Index++; $this->_${1}First = false; $this->_${1}Last = false; if ($this->_${1}Index == 1) { $this->_${1}First = true; } else { $this->_${1}First = false; } if ($this->_${1}Index == $_${1}Count) { $this->_${1}Last = true; } else { $this->_${1}Last = false; } if($this->_${1}Index % 2 == 0) { $this->_${1}Even = true; } else { $this->_${1}Even = false; }',
            '<?php $_${1}Source = ${2}; $_${1}Count = count((array)$_${1}Source); $this->_${1}Index = 0; foreach ( $_${1}Source as ${3} => ${4}): $this->_${1}Index++; $this->_${1}First = false; if ($this->_${1}Index == 1) { $this->_${1}First = true; } else { $this->_${1}First = false; } if ($this->_${1}Index == $_${1}Count) { $this->_${1}Last = true; } else { $this->_${1}Last = false; }'
        ], $input);

        /**
         * odzyskuje klamry
         */
        $input = str_replace([
            '__-angular-controller-__',
            '__-angular-start-__',
            '__-angular-end-__',
        ], [
            '{{$',
            '{{',
            '}}',
        ], $input);

        //fix dla funkcji JS'owych - badanie krótkich pod-ciągów
        $output = '';
        $php = false;
        for ($i = 0, $length = strlen($input); $i < $length; $i++) {
            if ($length > ($i + 5) && substr($input, $i, 5) == '<?php') {
                $php = true;
            } elseif ($length > ($i + 2) && substr($input, $i, 2) == '?>') {
                $php = false;
            }
            if (!$php && $length > ($i + 7) && substr($input, $i, 7) == '$this->') {
                $i += 7;
            }
            $output .= $input[$i];
        }
        return $output;
    }

    /**
     * Konwertuje określone tagi {@parametry@} na linki
     * przykład: {@module=cms&action=admin@} wygeneruje link do panelu administracyjnego
     * przykład 2: {@module=news@} wygeneruje link do strony głównej newsów
     * @param array $matches dopasowania
     * @return string
     */
    private function _url(array $matches)
    {
        $params = [];
        if (isset($matches[2])) {
            parse_str($matches[2], $params);
        }
        foreach ($params as $key => $param) {
            if ($param == 'null') {
                $params[$key] = null;
            }
        }
        $https = null;
        $flags = (isset($matches[1]) ? $matches[1] : null);
        switch (substr_count($flags, '*')) {
            case 1:
                $https = false;
                break;
            case 2:
                $https = true;
                break;
        }
        return \Mmi\App\FrontController::getInstance()->getView()->getHelper('url')->url($params, true, $https);
    }

    /**
     * Dekoduje linki z routera
     * @param array $matches dopasowania
     * @return String
     */
    private function _routerLinks(array $matches)
    {
        return str_replace(['%7B', '%3E', '%28', '%29', '%24', '%7D'], ['{', '>', '(', ')', '$', '}'], $matches[0]);
    }

    /**
     * Konwertuje określone tagi {{klucz_tekstu}} na tekst statyczny
     * @param array $matches dopasowania
     * @return string
     */
    private function _text(array $matches)
    {
        return \Mmi\App\FrontController::getInstance()->getView()->getHelper('text')->text($matches[1]);
    }

    /**
     * Konwertuje tagi {'element struktury'} na wynik renderowania pliku
     * przykład: {'cms/index'} wyrenderuje template index.tpl w module cms 
     * @param array $matches dopasowania
     * @return string
     */
    private function _render(array $matches)
    {
        //wyszukiwanie szablonu
        if (null === $template = $this->view->getTemplateByPath($matches[1])) {
            //brak szablonu
            return;
        }
        //rekurencyjne zastąpienie (gdyż wstawienia mogą być zagnieżdżone)
        return preg_replace_callback('/\{\'([a-zA-Z\-\/]+)\'\}/', [&$this, '_render'], file_get_contents($template));
    }

    /**
     * Konwertuje tagi {#tekst#} na wynik tłumaczenia tekstu
     * {#Hello World#} może wyrenderować Witaj Świecie, jeśli przetłumaczono
     * @param array $matches dopasowania
     * @return string
     */
    private function _translate(array $matches)
    {
        //tłumaczenie
        return \App\Registry::$translate->_($matches[1]);
    }

    /**
     * Konwertuje |filtr:"":"" na filtr
     * @param array $matches dopasowania
     * @return string
     */
    private function _filter(array $matches)
    {
        $filters = explode('|', $matches[0]);
        $var = $filters[0];
        unset($filters[0]);
        foreach ($filters as $filter) {
            $params = explode(':', $filter);
            $filterName = $params[0];
            unset($params[0]);
            $array = 'array(';
            foreach ($params as $param) {
                $array .= $param . ',';
            }
            $array = trim($array, ',') . ')';
            $var = 'getFilter(\'' . $filterName . '\')->setOptions(' . $array . ')->filter(' . $var . ')';
        }
        return $var;
    }

}
