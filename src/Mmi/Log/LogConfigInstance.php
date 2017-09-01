<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Log;

/**
 * Klasa konfiguracji elementu loggera
 * 
 * @method string getName() pobiera nazwę aplikacji logującej
 * @method LogConfigInstance setName($name) ustawia nazwę aplikacji logującej
 * @method string getPath() pobiera ścieżkę (lub kanał czy IP)
 * @method LogConfigInstance setPath($path) ustawia ścieżkę
 * @method string getHandler() pobiera handler
 * @method string getLevel() pobiera poziom logowania
 * @method string getToken() pobiera token
 * @method LogConfigInstance setToken($path) ustawia token
 */
class LogConfigInstance extends \Mmi\OptionObject
{

    /**
     * Domyślne ustawienia
     */
    public function __construct()
    {
        $this->setLevelDebug()
            ->setPath(BASE_PATH . '/var/log/app.log')
            ->setHandler('stream');
    }

    /**
     * Poziom na debug
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelDebug()
    {
        return $this->setOption('level', 100);
    }

    /**
     * Poziom na Info
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelInfo()
    {
        return $this->setOption('level', 200);
    }

    /**
     * Poziom na Notice
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelNotice()
    {
        return $this->setOption('level', 250);
    }

    /**
     * Poziom na Warning
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelWarning()
    {
        return $this->setOption('level', 300);
    }

    /**
     * Poziom na Error
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelError()
    {
        return $this->setOption('level', 400);
    }

    /**
     * Poziom na Alert
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelAlert()
    {
        return $this->setOption('level', 550);
    }

    /**
     * Poziom na Critical
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelCritical()
    {
        return $this->setOption('level', 500);
    }

    /**
     * Poziom na Emergency
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setLevelEmergency()
    {
        return $this->setOption('level', 600);
    }

    /**
     * Ustawia handler na stream
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setHandlerStream()
    {
        return $this->setOption('handler', 'stream');
    }

    /**
     * Ustawia handler na slack
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setHandlerSlack()
    {
        return $this->setOption('handler', 'slack');
    }

    /**
     * Ustawia handler na stream
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setHandlerConsole()
    {
        return $this->setOption('handler', 'console');
    }

    /**
     * Ustawia handler na gelf
     * @return \Mmi\Log\LogConfigInstance
     */
    public function setHandlerGelf()
    {
        return $this->setOption('handler', 'gelf');
    }

}
