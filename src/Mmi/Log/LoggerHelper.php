<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Log;

use Monolog\Logger,
    Monolog\Handler;

/**
 * Klasa nakładki na Monolog
 */
class LoggerHelper
{

    /**
     * Monolog instance to hold and use
     * @var \Monolog\Logger
     */
    protected static $_loggerInstance;

    /**
     * Konfiguracja loggera
     * @var LogConfig
     */
    protected static $_config;

    /**
     * Ustawia konfigurację loggera
     * @param \Mmi\Logger\LogConfig $config
     */
    public static function setConfig(LogConfig $config)
    {
        self::$_config = $config;
    }

    /**
     * Zwraca poziom logowania
     * @return integer
     */
    public static function getLevel()
    {
        if (!self::$_config) {
            throw new LoggerException('Configuration not loaded');
        }
        $minLevel = Logger::EMERGENCY;
        //iteracja po configach
        foreach (self::$_config as $config) {
            if ($config->getLevel() < $minLevel) {
                $minLevel = $config->getLevel();
            }
        }
        return $minLevel;
    }

    /**
     * Zwraca instancję helpera logowania
     * @return \Monolog\Logger
     */
    public static function getLogger()
    {
        if (self::$_loggerInstance) {
            return self::$_loggerInstance;
        }
        //konfiguruje loggera
        return self::$_loggerInstance = self::_configureLogger();
    }

    /**
     * Konfiguruje loggera
     * @return \Mmi\Logger\LoggerHelper
     */
    private static function _configureLogger()
    {
        //brak konfiguracji
        if (!self::$_config) {
            throw new LoggerException('Configuration not loaded');
        }
        //nowy obiekt loggera
        $logger = new Logger(self::$_config->getName());
        //iteracja po konfiguracjach instancji
        foreach (self::$_config as $config) {
            self::_configureInstance($config, $logger);
        }
        return $logger;
    }

    private static function _configureInstance(LogConfigInstance $config, Logger $logger)
    {
        //wybór handlera
        switch ($config->getHandler()) {
            case 'stream':
                $logger->pushHandler(new Handler\StreamHandler($config->getPath(), $config->getLevel()));
                break;
            case 'gelf':
                $logger->pushHandler(new Handler\GelfHandler(new \Gelf\Publisher($config->getPath())));
                break;
            case 'slack':
                $logger->pushHandler(new Handler\SlackHandler($config->getToken(), $config->getPath(), $logger->getName(), true, null, $config->getLevel()));
                break;
            case 'console':
                $logger->pushHandler(new Handler\PHPConsoleHandler([], null, $config->getLevel()));
                break;
            default:
                throw new LoggerException('Unknown handler');
        }
    }

}
