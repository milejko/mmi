<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Http\HttpServerEnv;
use Mmi\Http\Request;
use Mmi\Http\Response;
use Mmi\Mvc\ActionHelper;
use Mmi\Mvc\View;
use Psr\Log\LoggerInterface;

/**
 * Klasa obsługi zdarzeń PHP
 */
class AppEventHandler
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var HttpServerEnv
     */
    private $httpServerEnv;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var View
     */
    private $view;

    /**
     * @var ActionHelper
     */
    private $actionHelper;

    /**
     * Konstruktor podpinający eventy
     */
    public function __construct(
        LoggerInterface $logger,
        HttpServerEnv $httpServerEnv,
        Request $request,
        Response $response,
        View $view,
        ActionHelper $actionHelper
    )
    {
        //assigning injections
        $this->logger           = $logger;
        $this->httpServerEnv    = $httpServerEnv;
        $this->request          = $request;
        $this->response         = $response;
        $this->view             = $view;
        $this->actionHelper     = $actionHelper;
    }

    /**
     * Obsługuje błędy, ostrzeżenia
     * @throws KernelException
     */
    public function errorHandler(string $errno, string $errstr, string $errfile, string $errline): void
    {
        throw new KernelException($errno . ': ' . $errstr . '[' . $errfile . ' (' . $errline . ')]');
    }

    /**
     * Obsługuje wyjątki
     */
    public function exceptionHandler($exception): void
    {
        //próba czyszczenie bufora
        try {
            ob_clean();
        } catch (\Exception $e) {
            //nie było bufora
        }
        //logowanie wyjątku
        $this->logException($exception);
        //kod błędu ustawiany dla wyjątków poza nieodnalezionymi
        if (!($exception instanceof \Mmi\Mvc\MvcNotFoundException)) {
            //ustawienie kodu 500
            $this->response->setCodeError();
        }
        try {
            //widok
            $this->view->_exception = $exception;
            $this->view->_trace = $this->formatTrace($exception);
            //błąd bez layoutu lub nie HTML
            if ($this->view->isLayoutDisabled() || $this->response->getType() != \Mmi\Http\ResponseTypes::searchType('html')) {
                $this->logException($exception);
                //domyślna prezentacja błędów
                $this->response
                    ->setContent($this->rawErrorResponse($this->response, $exception->getMessage()))
                    ->send();
                return;
            }
            //błąd z prezentacją HTML
            $this->response->setContent($this->actionHelper->forward(new \Mmi\Http\Request(['module' => 'mmi', 'controller' => 'index', 'action' => 'error'])));
        } catch (\Exception $e) {
            $this->logException($exception);
            //domyślna prezentacja błędów
            $this->response->setContent($this->rawErrorResponse($this->response, $exception->getMessage()));
        }
        //send response
        $this->response->send();
    }

    /**
     * Zwraca sformatowany błąd dla danego typu odpowiedzi
     */
    private function rawErrorResponse(\Mmi\Http\Response $response): string
    {
        $message = '¯\_(ツ)_/¯ ups, something went wrong';
        //wybór typów
        switch ($response->getType()) {
                //plaintext
            case 'text/plain':
                return $message;
                //json
            case 'application/json':
                return json_encode([
                    'status' => 500,
                    'error' => $message,
                ]);
        }
        //domyślnie html
        return '<html><body><h1>Error 500</h1><p>' . $message . '</p></body></html>';
    }

    /**
     * Logowanie wyjątków
     */
    private function logException($exception): void
    {
        //logowanie wyjątku aplikacyjnego
        if ($exception instanceof \Mmi\App\KernelException) {
            $this->logger->log($exception->getCode(), $this->formatException($exception));
            return;
        }
        //logowanie pozostałych wyjątków
        $this->logger->alert($this->formatException($exception));
    }

    /**
     * Formatuje obiekt wyjątku do pojedynczej wiadomości
     */
    private function formatException($exception): string
    {
        return str_replace(realpath(BASE_PATH), '', $this->httpServerEnv->requestUri . ' (' . $exception->getMessage() . ') @' .
            $exception->getFile() . '(' . $exception->getLine() . ') ' .
            $this->formatTrace($exception));
    }

    /**
     * Format trace
     */
    private function formatTrace($exception): string
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
