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
use \Mmi\Message\MessengerHelper;
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
     * Konstruktor
     */
    public function __construct(
        Response $response, 
        View $view,
        AppProfilerInterface $profiler,
        LoggerInterface $logger
    )
    {
        //injections
        $this->view     = $view;
        $this->response = $response;
        $this->profiler = $profiler;
        $this->logger   = $logger;
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
     * @return \Mmi\Http\Response
     */
    public final function getResponse()
    {
        return $this->response;
    }

    /**
     * Pobiera helper messengera
     * @return \Mmi\Message\Messenger
     */
    public final function getMessenger()
    {
        return MessengerHelper::getMessenger();
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
