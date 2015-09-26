<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;
use Monolog\Logger;

class EventHandler {
	
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
	 * Handler zamknięcia aplikacji
	 */
	public static function shutdownHandler() {
		//bez błędów
		if (null === $error = error_get_last()) {
			//logowanie danych debuggerów
			return self::_logDebugData();
		}
		//pobranie odpowiedzi z front kontrolera
		$response = \Mmi\App\FrontController::getInstance()->getResponse();
		//wysłanie błędu
		$response->setCodeError()
			->setContent(self::_rawErrorResponse($response, $error['message'], $error['file'] . ' [' . $error['line'] . ']'))
			->send();
		//logowanie emergency
		LoggerHelper::getLogger()->addEmergency($error['message']);
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
		self::_logException($exception);
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
				->setContent($view->setPlaceholder('content', \Mmi\Mvc\ActionHelper::getInstance()->action(['module' => 'mmi', 'controller' => 'index', 'action' => 'error']))
					->renderLayout('mmi', 'index'))->send();
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
	private static function _sendRawResponse(\Mmi\Http\Response $response, \Exception $exception) {
		$response->setCodeError()
			->setContent(self::_rawErrorResponse($response, $exception->getMessage(), $exception->getTraceAsString()))
			->send();
	}
	
	/**
	 * Zwraca sformatowany błąd dla danego typu odpowiedzi
	 * @param \Mmi\Http\Response $response obiekt odpowiedzi
	 * @param string $title
	 * @param string $body
	 * @return mixed
	 */
	private static function _rawErrorResponse(\Mmi\Http\Response $response, $title, $body) {
		switch ($response->getType()) {
			//typy HTML
			case 'htm':
			case 'html':
			case 'shtml':
				return '<html><body><h1>' . $title . '</h1>' . nl2br($body) . '</body></html>';
			//plaintext
			case 'txt':
				return $title . "\n" . $body . "\n";
			//json
			case 'json':
				return json_encode([
					'status' => 500,
					'error' => $title,
					'exception' => $body,
				]);
		}
	}
	
	/**
	 * Logowanie wyjątków
	 * @param \Exception $exception
	 */
	private static function _logException(\Exception $exception) {
		if ($exception instanceof \Mmi\App\Exception) {
			LoggerHelper::getLogger()->addRecord($exception->getCode(), $exception->getMessage() . ' ' . $exception->getTraceAsString());
		}
		//logowanie błędu
		LoggerHelper::getLogger()->addError($exception->getMessage() . ' ' . $exception->getTraceAsString());
	}
	
	/**
	 * Loguje dane z debuggera
	 */
	private static function _logDebugData() {
		if (LoggerHelper::getLevel() > Logger::DEBUG) {
			return;
		}
		LoggerHelper::getLogger()->addDebug('[' . round(Profiler::elapsed(), 5) . '] all events - ' . FrontController::getInstance()->getEnvironment()->requestUri);
		LoggerHelper::getLogger()->addDebug('[' . round(\Mmi\Db\Profiler::elapsed(), 5) . '] sql queries - ' . FrontController::getInstance()->getEnvironment()->requestUri);
	}

}
