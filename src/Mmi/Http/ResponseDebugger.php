<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

use Mmi\App\FrontController;

/**
 * Klasa panelu debugowania aplikacji
 */
class ResponseDebugger
{

    //pre z łamaniem linii
    CONST PRE_OPEN_BREAK = '<pre style="white-space: normal; word-wrap: break-word; margin: 0px 0px 10px 0px; color: #666; background: #eee; padding: 3px; border: 1px solid #666;">';
    //domyślny pre
    CONST PRE_OPEN = '<pre style="min-width: 450px; margin: 0px 0px 10px 0px; color: #666; background: #eee; padding: 3px; border: 1px solid #666;">';

    /**
     * Konstruktor - modyfikuje response o dane debbugera
     */
    public function __construct()
    {
        $response = FrontController::getInstance()->getResponse();
        //html
        if ('text/html' == $response->getType()) {
            //ustawianie contentu z debuggerem
            $response->setContent(str_replace('</body>', $this->getHtml() . '</body>', $response->getContent()));
        }
    }

    /**
     * Czas wykonania skryptu w sekundach
     * @return string
     */
    protected function _getElapsed()
    {
        return round(FrontController::getInstance()->getProfiler()->elapsed(), 4) . 's';
    }

    /**
     * Maksymalne zużycie pamięci
     * @return string
     */
    protected function _getPeakMemory()
    {
        return round(memory_get_peak_usage() / (1024 * 1024), 2) . 'MB';
    }

    /**
     * Zwraca panel HTML
     * @return string
     */
    public function getHtml()
    {
        //pobranie widoku
        $view = \Mmi\App\FrontController::getInstance()->getView();
        if ($view->getCache() === null || !$view->getCache()->isActive()) {
            $cacheInfo = '<span style="color: #f22;">no cache</span>';
        } else {
            $cacheInfo = '<span style="color: #99ff99;">cache on</span>';
        }
        //czasy i pamięci w wykonaniu
        $html = "\n";
        $html .= '<style>div#MmiPanel pre, div#MmiPanel table, div#MmiPanel table tr, div#MmiPanel table td, div#MmiPanel div, div#MmiPanel p {font: normal 11px Monospace!important;}</style><div id="MmiPanelBar" onclick="document.getElementById(\'MmiPanel\').style.display=\'block\'; window.scrollTo(0,document.getElementById(\'MmiPanel\').offsetTop);" style="';
        $html .= 'text-align: center; position: fixed; padding: 0 10px; margin: 0; line-height: 0; background: #999; border-radius: 5px 5px 0 0; font: bold 10px Arial!important; color: #000; bottom: 0px; left: 45%; text-transform: none;">' . $this->_getElapsed() . ', ' . $this->_getPeakMemory() . ' - ' . $cacheInfo . '</div>';
        $html .= '<div id="MmiPanel" ondblclick="this.style.display=\'none\';" style="';
        if (null === $view->_exception) {
            $html .= 'display: none; ';
        }
        //rozszerzony podgląd
        $html .= 'position: relative; text-align: left; padding: 20px 10px 5px 10px; background: #ccc; color: #000; font: normal 11px Monospace!important;">';
        if (null !== $view->_exception) {
            $html .= '<h2 style="color: #bb0000; margin: 0px; font-size: 14px; text-transform: none;">' . get_class($view->_exception) . ': ' . $view->_exception->getMessage() . '</h2>';
            $html .= '<p style="margin: 0px; padding: 0px 0px 10px 0px;">' . $view->_exception->getFile() . ' <strong>(' . $view->_exception->getLine() . ')</strong></p>';
            $html .= '<pre>' . $view->_trace . '</pre><br />';
        }
        $html .= '<table cellspacing="0" cellpadding="0" border="0" style="width: 100%; padding: 0px; margin: 0px;"><tr><td style="vertical-align: top; padding-right: 5px;">';

        //środowisko
        $html .= '<p style="margin: 0px;">Environment:</p>';
        $html .= self::PRE_OPEN_BREAK . '<p style="margin: 0; padding: 0;">Time: <b>' . $this->_getElapsed() . ' (' . $this->_getPeakMemory() . ', ' . $cacheInfo . ')</b></p>';
        $html .= ResponseDebugger\Part::getEnvHtml() . '</pre>';

        //konfiguracja
        $html .= '<p style="margin: 0px;">Configuration:</p>';
        $html .= self::PRE_OPEN . ResponseDebugger\Part::getConfigHtml() . '</pre>';

        //profiler aplikacji
        $html .= '<p style="margin: 0px;">Kernel Profiler: </p>';
        $html .= self::PRE_OPEN . ResponseDebugger\Part::getProfilerHtml() . '</pre>';

        //profiler bazy danych
        $html .= '<p style="margin: 0px;">Database Profiler: </p>';
        $html .= self::PRE_OPEN . ResponseDebugger\Part::getDbProfilerHtml() . '</pre>';

        $html .= '</td><td style="vertical-align: top; padding-left: 5px;">';

        //zmienne requesta
        $html .= '<p style="margin: 0px;">Request Variables: </p>';
        $html .= self::PRE_OPEN;
        $html .= ResponseDebugger\Colorify::colorify(print_r(\Mmi\App\FrontController::getInstance()->getRequest()->toArray(), true)) . '</pre>';

        //zmienne rejestru
        $html .= '<p style="margin: 0px;">Registry Variables: </p>';
        $html .= self::PRE_OPEN;
        $html .= ResponseDebugger\Colorify::colorify(print_r($this->_simplifyVarArray(get_class_vars('\App\Registry')), true)) . '</pre>';

        //zmienne widoku
        if ($view !== null) {
            $html .= '<p style="margin: 0px;">View Variables: </p>';
            $html .= self::PRE_OPEN;
            $html .= ResponseDebugger\Colorify::colorify(print_r($this->_simplifyVarArray($view->getAllVariables()), true)) . '</pre>';
        }

        //zmienne cookie
        if (isset($_COOKIE) && count($_COOKIE) > 0) {
            $html .= '<p style="margin: 0px;">Cookie Variables: </p>';
            $html .= self::PRE_OPEN;
            $html .= ResponseDebugger\Colorify::colorify(print_r($this->_simplifyVarArray($_COOKIE), true)) . '</pre>';
        }
        //zmienne sesji
        if (isset($_SESSION) && count($_SESSION) > 0) {
            $html .= '<p style="margin: 0px;">Session Variables: </p>';
            $html .= self::PRE_OPEN;
            $html .= ResponseDebugger\Colorify::colorify(print_r($this->_simplifyVarArray($_SESSION), true)) . '</pre>';
        }
        $html .= '</pre>';
        $html .= '</td></tr></table></div>';
        return $html;
    }

    /**
     * Skracanie zmiennych
     * @param array $vars
     * @return array
     */
    protected function _simplifyVarArray(array $vars, $depth = 0)
    {
        $simplifiedVars = [];
        //pętla po tablicy
        foreach ($vars as $varName => $varValue) {
            //jeśli jest obiektem, uproszczenie do jego nazwy
            if (is_object($varValue)) {
                $simplifiedVars[$varName] = 'Object { ' . get_class($varValue) . ' }';
                continue;
            }
            //jeśli jest tablicą - rekurencyjne zejście
            if (is_array($varValue)) {
                ($depth < 2 && count($varValue) < 10) ? $simplifiedVars[$varName] = $this->_simplifyVarArray($varValue, $depth + 1) : $simplifiedVars[$varName] = 'Array(...)';
                continue;
            }
            //jeśli jest zwykłą zmienną - bez zmian
            $simplifiedVars[$varName] = $varValue;
        }
        //zwrot wartości
        return $simplifiedVars;
    }

}
