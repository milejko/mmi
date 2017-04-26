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
class KernelEventHandler
{

    /**
     * Konstruktor podpinający eventy
     */
    public function __construct()
    {
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
    public static function errorHandler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        throw new KernelException($errno . ': ' . $errstr . '[' . $errfile . ' (' . $errline . ')]');
    }

    /**
     * Handler zamknięcia aplikacji
     */
    public static function shutdownHandler()
    {
        //bez błędów
        if (null == $error = error_get_last()) {
            return;
        }
        //pobranie odpowiedzi z front kontrolera
        $response = \Mmi\App\FrontController::getInstance()->getResponse();
        //logowanie błędu Emergency
        FrontController::getInstance()->getLogger()->addEmergency($error['message']);
        //wysyłanie odpowiedzi
        return self::_sendResponse($response->setContent(self::_rawErrorResponse($response)));
    }

    /**
     * Obsługuje wyjątki
     * @param \Exception $exception wyjątek
     * @return boolean
     */
    public static function exceptionHandler($exception)
    {
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
        //kod błędu ustawiany dla wyjątków poza nieodnalezionymi
        if (!($exception instanceof \Mmi\Mvc\MvcNotFoundException)) {
            //ustawienie kodu 500
            $response->setCodeError();
        }
        try {
            //widok
            $view = \Mmi\App\FrontController::getInstance()->getView();
            $view->_exception = $exception;
            $view->_trace = self::_formatTrace($exception);
            //błąd bez layoutu lub nie HTML
            if ($view->isLayoutDisabled() || $response->getType() != 'html') {
                //domyślna prezentacja błędów
                return self::_sendRawResponse($response, $exception);
            }
            //błąd z prezentacją HTML
            return self::_sendResponse($response->setContent(\Mmi\Mvc\ActionHelper::getInstance()->forward(new \Mmi\Http\Request(['module' => 'mmi', 'controller' => 'index', 'action' => 'error']))));
        } catch (\Exception $e) {
            //domyślna prezentacja błędów
            return self::_sendRawResponse($response, $exception);
        }
    }

    /**
     * Wysyłanie contentu
     * @param \Mmi\Http\Response $response
     */
    private static function _sendResponse(\Mmi\Http\Response $response)
    {
        $response->send();
        return true;
    }

    /**
     * Wysyła surowy content
     * @param type $response
     * @param \Exception $exception
     */
    private static function _sendRawResponse(\Mmi\Http\Response $response, $exception)
    {
        return self::_sendResponse($response->setContent(self::_rawErrorResponse($response, $exception->getMessage(), self::_logException($exception))));
    }

    /**
     * Zwraca sformatowany błąd dla danego typu odpowiedzi
     * @param \Mmi\Http\Response $response obiekt odpowiedzi
     * @return mixed
     */
    private static function _rawErrorResponse(\Mmi\Http\Response $response)
    {
        switch ($response->getType()) {
            //typy HTML
            case 'htm':
            case 'html':
            case 'shtml':
                return '<html><body><h1>Error 500</h1><p>Something went wrong</p></body></html>';
            //plaintext
            case 'txt':
                return 'Error 500' . "\n" . 'Something went wrong' . "\n";
            //json
            case 'json':
                return json_encode([
                    'status' => 500,
                    'error' => 'something went wrong',
                ]);
        }
    }

    /**
     * Logowanie wyjątków
     * @param \Exception $exception
     */
    private static function _logException($exception)
    {
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
    private static function _formatException($exception)
    {
        return str_replace(realpath(BASE_PATH), '', \Mmi\App\FrontController::getInstance()->getEnvironment()->requestUri . ' (' . $exception->getMessage() . ') @' .
            $exception->getFile() . '(' . $exception->getLine() . ') ' .
            self::_formatTrace($exception));
    }

    /**
     * Format trace
     * @param \Exception $exception
     * @return string
     */
    private static function _formatTrace($exception)
    {
        $message = '';
        $i = 0;
        $trace = $exception->getTrace();
        array_shift($trace);
        foreach ($trace as $row) {
            $i++;
            $message .= "\n" . '#' . $i;
            $message .= isset($row['file']) ? ' ' . $row['file'] : '';
            $message .= isset($row['line']) ? '(' . $row['line'] . ')' : '';
            $message .= isset($row['class']) ? ' ' . $row['class'] . '::' : '';
            $message .= isset($row['function']) ? (isset($row['class']) ? '' : ' ') . $row['function'] . '(' : '';
            $message .= isset($row['args']) ? json_encode($row['args']) . ')' : '';
        }
        return $message;
    }

}
