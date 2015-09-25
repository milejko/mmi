<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\Controller;

/**
 * Klasa panelu debugowania aplikacji
 */
class ResponseDebugger {

	//pre z łamaniem linii
	CONST PRE_OPEN_BREAK = '<pre style="white-space: normal; word-wrap: break-word; margin: 0px 0px 10px 0px; color: #666; background: #eee; padding: 3px; border: 1px solid #666;">';
	//domyślny pre
	CONST PRE_OPEN = '<pre style="min-width: 450px; margin: 0px 0px 10px 0px; color: #666; background: #eee; padding: 3px; border: 1px solid #666;">';

	/**
	 * Konstruktor - modyfikuje response o dane debbugera
	 */
	public function __construct() {
		$response = \Mmi\App\FrontController::getInstance()->getResponse();
		//odpowiedź nie jest znakowa
		if (!is_string($response->getContent())) {
			return;
		}
		switch ($response->getType()) {
			//html
			case 'htm':
			case 'html':
			case 'shtml':
				//ustawianie contentu z debuggerem
				$response->setContent(str_replace('</body>', $this->getHtml() . '</body>', $response->getContent()));
				return;
			//json
			case 'json':
				try {
					if (strpos($response->getContent(), '}')) {
						$lastBracket = strrpos($response->getContent(), '}');
						$response->setContent(substr($response->getContent(), 0, $lastBracket) . ',"debugger":' . json_encode($this->getSummaryArray()) . substr($response->getContent(), $lastBracket));
					}
				} catch (\Exception $e) {
					return;
				}
		}
	}

	/**
	 * Tablica z podsumowaniem: czas wykonania i maksymalne użycie pamięci
	 * @return array
	 */
	public function getSummaryArray() {
		return [
			'elapsed' => $this->_getElapsed(),
			'memory' => $this->_getPeakMemory()
		];
	}

	/**
	 * Czas wykonania skryptu w sekundach
	 * @return string
	 */
	protected function _getElapsed() {
		return round(\Mmi\Profiler::elapsed(), 4) . 's';
	}

	/**
	 * Maksymalne zużycie pamięci
	 * @return string
	 */
	protected function _getPeakMemory() {
		return round(memory_get_peak_usage() / (1024 * 1024), 2) . 'MB';
	}

	/**
	 * Zwraca panel HTML
	 * @return string
	 */
	public function getHtml() {
		$view = \Mmi\App\FrontController::getInstance()->getView();
		if ($view->getCache() === null || !$view->getCache()->isActive()) {
			$cacheInfo = '<span style="color: #f22;">no cache</span>';
		} else {
			$cacheInfo = '<span style="color: #99ff99;">cache on</span>';
		}
		$html = "\n";
		$html .= '<style>div#MmiPanel pre, div#MmiPanel table, div#MmiPanel table tr, div#MmiPanel table td, div#MmiPanel div, div#MmiPanel p {font: normal 11px Monospace!important;}</style><div id="MmiPanelBar" onclick="document.getElementById(\'MmiPanel\').style.display=\'block\'; window.scrollTo(0,document.getElementById(\'MmiPanel\').offsetTop);" style="';
		$html .= 'text-align: center; position: fixed; padding: 0 10px; margin: 0; line-height: 0; background: #999; border-radius: 5px 5px 0 0; font: bold 10px Arial!important; color: #000; bottom: 0px; left: 45%; text-transform: none;">' . $this->_getElapsed() . ', ' . $this->_getPeakMemory() . ' - ' . $cacheInfo . '</div>';
		$html .= '<div id="MmiPanel" ondblclick="this.style.display=\'none\';" style="';
		/* @var $view->_exception Exception */
		if (null === $view->_exception) {
			$html .= 'display: none; ';
		}
		$html .= 'position: relative; text-align: left; padding: 20px 10px 5px 10px; background: #ccc; color: #000; font: normal 11px Monospace!important;">';
		if (null !== $view->_exception) {
			$html .= '<h2 style="color: #bb0000; margin: 0px; font-size: 14px; text-transform: none;">' . get_class($view->_exception) . ': ' . $view->_exception->getMessage() . '</h2>';
			$html .= '<p style="margin: 0px; padding: 0px 0px 10px 0px;">' . $view->_exception->getFile() . ' <strong>(' . $view->_exception->getLine() . ')</strong></p>';
			$html .= '<pre>' . $view->_exception->getTraceAsString() . '</pre><br />';
		}
		$html .= '<table cellspacing="0" cellpadding="0" border="0" style="width: 100%; padding: 0px; margin: 0px;"><tr><td style="vertical-align: top; padding-right: 5px;">';


		//środowisko
		$html .= '<p style="margin: 0px;">Environment:</p>';
		$html .= self::PRE_OPEN_BREAK . '<p style="margin: 0; padding: 0;">Time: <b>' . $this->_getElapsed() . ' (' . $this->_getPeakMemory() . ', ' . $cacheInfo . ')</b></p>';
		$html .= ResponseDebugger\Part::getEnvHtml() . '</pre>';

		//konfiguracja
		$html .= '<p style="margin: 0px;">Configuration:</p>';
		$html .= self::PRE_OPEN . ResponseDebugger\Part::getConfigHtml() . '</pre>';

		//profiler DB
		$html .= '<p style="margin: 0px;">SQL queries: <b>' . \Mmi\Db\Profiler::count() . '</b>, elapsed time: <b>' . round(\Mmi\Db\Profiler::elapsed(), 4) . 's </b></p>';
		$html .= self::PRE_OPEN_BREAK . ResponseDebugger\Part::getDbHtml() . '</pre>';

		//profiler aplikacji
		$html .= '<p style="margin: 0px;">PHP Profiler: </p>';
		$html .= self::PRE_OPEN . ResponseDebugger\Part::getProfilerHtml() . '</pre>';
		
		//opcache lub APC
		$html .= '<p style="margin: 0px;">PHP precompiler</p>';
		$html .= self::PRE_OPEN . ResponseDebugger\Opcache::getHtml() . '</pre>';
		
		//dołączanie rozszerzeń
		$html .= '<p style="margin: 0px">Loaded extensions:</p>';
		$html .= self::PRE_OPEN . ResponseDebugger\Part::getExtensionHtml() . '</pre>';

		$html .= '</td><td style="vertical-align: top; padding-left: 5px;">';

		//zmienne requesta
		$html .= '<p style="margin: 0px;">Request Variables: </p>';
		$html .= self::PRE_OPEN;
		$html .= ResponseDebugger\Colorify::colorify(print_r(\Mmi\App\FrontController::getInstance()->getRequest()->toArray(), true)) . '</pre>';

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
			$html .= ResponseDebugger\Colorify::colorify(print_r($_COOKIE, true)) . '</pre>';
		}
		//zmienne sesji
		if (isset($_SESSION) && count($_SESSION) > 0) {
			$html .= '<p style="margin: 0px;">Session Variables: </p>';
			$html .= self::PRE_OPEN;
			$html .= ResponseDebugger\Colorify::colorify(print_r($_SESSION, true)) . '</pre>';
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
	protected function _simplifyVarArray($vars) {
		//jeśli nie jest tablicą
		if (!is_array($vars)) {
			return $vars;
		}
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
				$simplifiedVars[$varName] = $this->_simplifyVarArray($varValue);
				continue;
			}
			//jeśli jest zwykłą zmienną - bez zmian
			$simplifiedVars[$varName] = $varValue;
		}
		return $simplifiedVars;
	}

}
