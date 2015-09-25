<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

class ErrorHandler {

	/**
	 * Obsługuje błędy, ostrzeżenia
	 * @param string $errno numer błędu
	 * @param string $errstr treść błędu
	 * @param string $errfile plik
	 * @param string $errline linia z błędem
	 * @param string $errcontext kontekst
	 * @return boolean
	 */
	public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
		throw new Exception($errno . ': ' . $errstr . '[' . $errfile . ' (' . $errline . ')]');
	}

	/**
	 * Obsługuje wyjątki
	 * @param Exception $exception wyjątek
	 * @return boolean
	 */
	public static function exceptionHandler(\Exception $exception) {
		//czyszczenie bufora
		try {
			ob_clean();
		} catch (\Exception $e) {
			//brak bufora - tworzenie
			ob_start();
		}
		//logowanie błędu
		\Mmi\App\ExceptionLogger::log($exception);
		$response = \Mmi\App\FrontController::getInstance()->getResponse();
		try {
			//widok
			$view = \Mmi\App\FrontController::getInstance()->getView();
			$view->_exception = $exception;
			//błąd bez layoutu lub nie HTML
			if ($view->isLayoutDisabled() || $response->getType() != 'html') {
				//domyślna prezentacja błędów
				self::_sendRawResponse($response, $exception);
				return true;
			}
			//błąd z prezentacją HTML
			$response->setCodeError()
				->setContent($view->setPlaceholder('content', \Mmi\Mvc\ActionPerformer::getInstance()->action(['module' => 'mmi', 'controller' => 'index', 'action' => 'error']))
					->renderLayout('mmi', 'index'))
				->send();
			return true;
		} catch (\Exception $e) {
			//domyślna prezentacja błędów
			self::_sendRawResponse($response, $exception);
		}
		return true;
	}

	/**
	 * 
	 * @param type $response
	 * @param Exception $exception
	 */
	private static function _sendRawResponse(\Mmi\Mvc\Controller\Response $response, \Exception $exception) {
		$response->setCodeError()
			->setContent(self::_rawErrorResponse($response, $exception))
			->send();
	}

	/**
	 * Zwraca sformatowany błąd dla danego typu odpowiedzi
	 * @param \Mmi\Mvc\Controller\Response $response obiekt odpowiedzi
	 * @param \Exception $e wyjątek
	 * @return mixed
	 */
	private static function _rawErrorResponse(\Mmi\Mvc\Controller\Response $response, \Exception $e) {
		switch ($response->getType()) {
			//typy HTML
			case 'htm':
			case 'html':
			case 'shtml':
				return '<html><body><h1>' . $e->getMessage() . '</h1>' . nl2br($e->getTraceAsString()) . '</body></html>';
			//plaintext
			case 'txt':
				return $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
			//json
			case 'json':
				return json_encode([
					'status' => 500,
					'error' => $e->getMessage(),
					'exception' => $e->getTraceAsString(),
				]);
		}
	}

}
