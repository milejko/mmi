<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

/**
 * Klasa obsługi zdarzeń PHP
 */
class KernelEventHandler {

	/**
	 * Konstruktor podpinający eventy
	 */
	public function __construct() {
		//funkcja  zamknięcia aplikacji
		register_shutdown_function(['\\Mmi\\App\\KernelEventHandler', 'shutdownHandler']);
		//domyślne przechwycenie wyjątków
		set_exception_handler(['\\Mmi\\App\\KernelEventHandler', 'exceptionHandler']);
		//domyślne przechwycenie błędów
		set_error_handler(['\\Mmi\\App\\KernelEventHandler', 'errorHandler']);
	}

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
		throw new KernelException($errno . ': ' . $errstr . '[' . $errfile . ' (' . $errline . ')]');
	}

	/**
	 * Handler zamknięcia aplikacji
	 */
	public static function shutdownHandler() {
		//bez błędów
		if (null == $error = error_get_last()) {
			return;
		}
		//pobranie odpowiedzi z front kontrolera
		$response = \Mmi\App\FrontController::getInstance()->getResponse();
		//logowanie błędu Emergency
		FrontController::getInstance()->getLogger()->addEmergency($error['message']);
		//wysyłanie odpowiedzi
		return self::_sendResponse($response->setContent(self::_rawErrorResponse($response, $error['message'], $error['file'] . ' [' . $error['line'] . ']')));
	}

	/**
	 * Obsługuje wyjątki
	 * @param \Exception $exception wyjątek
	 * @return boolean
	 */
	public static function exceptionHandler(\Throwable $exception) {
		//czyszczenie bufora
		try {
			ob_clean();
		} catch (\Exception $e) {
			//brak bufora - tworzenie
			ob_start();
		}
		//logowanie wyjątku
		self::_logException($exception);
		$response = \Mmi\App\FrontController::getInstance()->getResponse();
		try {
			//widok
			$view = \Mmi\App\FrontController::getInstance()->getView();
			$view->_exception = $exception;
			//błąd bez layoutu lub nie HTML
			if ($view->isLayoutDisabled() || $response->getType() != 'html') {
				//domyślna prezentacja błędów
				return self::_sendRawResponse($response, $exception);
			}
			//błąd z prezentacją HTML
			return self::_sendResponse($response->setContent($view->setPlaceholder('content', \Mmi\Mvc\ActionHelper::getInstance()->action(['module' => 'mmi', 'controller' => 'index', 'action' => 'error']))
							->renderLayout('mmi', 'index')));
		} catch (\Exception $e) {
			//domyślna prezentacja błędów
			return self::_sendRawResponse($response, $exception);
		}
	}

	/**
	 * Wysyłanie contentu
	 * @param \Mmi\Http\Response $response
	 */
	private static function _sendResponse(\Mmi\Http\Response $response) {
		$response->setCodeError()
			->send();
		return true;
	}

	/**
	 * Wysyła surowy content
	 * @param type $response
	 * @param \Exception $exception
	 */
	private static function _sendRawResponse(\Mmi\Http\Response $response, \Throwable $exception) {
		return self::_sendResponse($response->setContent(self::_rawErrorResponse($response, $exception->getMessage(), $exception->getTraceAsString())));
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
	private static function _logException(\Throwable $exception) {
		//logowanie wyjątku aplikacyjnego
		if ($exception instanceof \Mmi\App\KernelException) {
			FrontController::getInstance()->getLogger()->addRecord($exception->getCode(), self::_formatException($exception));
			return;
		}
		//logowanie pozostałych wyjątków
		FrontController::getInstance()->getLogger()->addAlert(self::_formatException($exception));
	}

	/**
	 * Formatuje obiekt wyjątku do pojedynczej wiadomości
	 * @param \Exception $exception
	 * @return string
	 */
	private static function _formatException(\Throwable $exception) {
		return str_replace(realpath(BASE_PATH), '', \Mmi\App\FrontController::getInstance()->getEnvironment()->requestUri . ' (' . $exception->getMessage() . ') @' .
			$exception->getFile() . '(' . $exception->getLine() . ') ' .
			$exception->getTraceAsString());
	}

}
