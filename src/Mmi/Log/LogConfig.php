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
 * Klasa konfiguracji loggera
 * 
 * @method LogConfigInstance next()
 * @method LogConfigInstance current()
 * @method LogConfigInstance rewind()
 */
class LogConfig extends \Mmi\DataObject
{

    /**
     * Nazwa loggera
     * @var string
     */
    public $_name = 'App';

    /**
     * Dodaje element nawigatora
     * @param LogConfigInstance $instance
     * @return \Mmi\Log\LogConfig
     */
    public function addInstance(LogConfigInstance $instance)
    {
        $this->_data[] = $instance;
        return $this;
    }

    /**
     * Zablokowany setter
     * @param string $key
     * @param mixed $value
     * @throws LoggerException
     */
    public function __set($key, $value)
    {
        throw new LoggerException('Unable to set: {' . $key . '} to value = ' . $value);
    }

    /**
     * Nazwa loggera
     * @param string $name
     * @return \Mmi\Log\Config
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Pobiera nazwę loggera
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

}
