<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
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
     * Instancja loggera
     * @var \Psr\Log\LoggerInterface
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
     * @return \Psr\Log\LoggerInterface
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
            //tworzy pustą konfigurację
            return new \Psr\Log\NullLogger;
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
            case 'syslog':
                $logger->pushHandler(new Handler\SyslogHandler('mmi', LOG_USER, $config->getLevel()));
                break;
            default:
                throw new LoggerException('Unknown handler');
        }
    }

}
