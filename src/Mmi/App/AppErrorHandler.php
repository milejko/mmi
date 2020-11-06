<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Http\Request;
use Mmi\Http\Response;
use Mmi\Http\ResponseTypes;
use Mmi\Mvc\ActionHelper;
use Mmi\Mvc\MvcNotFoundException;
use Mmi\Mvc\View;

/**
 * Application error handler
 */
class AppErrorHandler
{

    /**
     * @var AppExceptionLogger
     */
    private $logger;

    /**
     * @var AppExceptionFormatter
     */
    private $formatter;

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
        AppExceptionLogger $logger,
        AppExceptionFormatter $formatter,
        Response $response,
        View $view,
        ActionHelper $actionHelper
    )
    {
        //assigning injections
        $this->logger           = $logger;
        $this->formatter        = $formatter;
        $this->response         = $response;
        $this->view             = $view;
        $this->actionHelper     = $actionHelper;
    }

    /**
     * Errors, warnings, notices, etc. as exception
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
        $this->logger->logException($exception);
        //kod błędu ustawiany dla wyjątków poza nieodnalezionymi
        if (!($exception instanceof MvcNotFoundException)) {
            //ustawienie kodu 500
            $this->response->setCodeError();
        }
        try {
            //widok
            $this->view->_exception = $exception;
            $this->view->_trace = $this->formatter->formatTrace($exception);
            //błąd bez layoutu lub nie HTML
            if ($this->view->isLayoutDisabled() || $this->response->getType() != ResponseTypes::searchType('html')) {
                //domyślna prezentacja błędów
                $this->response
                    ->setContent($this->rawErrorResponse($this->response))
                    ->send();
                return;
            }
            //błąd z prezentacją HTML
            $this->response->setContent($this->actionHelper->forward(new Request(['module' => 'mmi', 'controller' => 'index', 'action' => 'error'])));
        } catch (\Exception $renderException) {
            $this->logger->logException($renderException);
            //domyślna prezentacja błędów
            $this->response->setContent($this->rawErrorResponse($this->response));
        }
        //send response
        $this->response->send();
    }

    /**
     * Zwraca sformatowany błąd dla danego typu odpowiedzi
     */
    private function rawErrorResponse(Response $response): string
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

}
