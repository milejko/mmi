<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\AppProfilerInterface;
use Mmi\Http\Response;
use Psr\Log\LoggerInterface;

/**
 * Klasa kontrolera akcji
 */
class Controller
{

    /**
     * Widok
     * @var View
     */
    public $view;

    /**
     * Referencja do odpowiedzi
     * @var Response
     */
    private $response;

    /**
     * @var AppProfilerInterface
     */
    private $profiler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Messenger
     */
    private $messenger;

    /**
     * Konstruktor
     */
    public function __construct(
        Response $response, 
        View $view,
        AppProfilerInterface $profiler,
        LoggerInterface $logger,
        Messenger $messenger
    )
    {
        //injections
        $this->view         = $view;
        $this->response     = $response;
        $this->profiler     = $profiler;
        $this->logger       = $logger;
        $this->messenger    = $messenger;
        //init method
        $this->init();
    }

    /**
     * Funkcja dla użytkownika ładowana na końcu konstruktora
     */
    public function init()
    {
        
    }

    /**
     * Pobiera response
     */
    public final function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Pobiera helper messengera
     */
    public final function getMessenger(): Messenger
    {
        return $this->messenger;
    }

    /**
     * Pobiera helper logowania
     */
    public final function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Pobiera profiler
     */
    public final function getProfiler(): AppProfilerInterface
    {
        return $this->profiler;
    }

}
