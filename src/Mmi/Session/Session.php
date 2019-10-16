<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

use Mmi\App\FrontController;

class Session
{

    const FILE_HANDLER = 'files';
    const REDIS_HANDLER = 'redis';

    /**
     * Rozpoczęcie sesji
     * @param \Mmi\Session\SessionConfig $config
     * @throws SessionException
     */
    public static function start(\Mmi\Session\SessionConfig $config)
    {
        session_name($config->name);
        session_set_cookie_params($config->cookieLifetime, $config->cookiePath, $config->cookieDomain, $config->cookieSecure, $config->cookieHttpOnly);
        session_cache_expire($config->cacheExpire);
        session_save_path($config->path);
        ini_set('session.gc_divisor', $config->gcDivisor);
        ini_set('session.gc_maxlifetime', $config->gcMaxLifetime);
        ini_set('session.gc_probability', $config->gcProbability);
        if ($config->handler == self::FILE_HANDLER || $config->handler == self::FILE_HANDLER) {
            ini_set('session.save_handler', $config->handler);
        } else {
            $handlerClass = $config->handler;
            session_set_save_handler(new $handlerClass());
        }
        session_start();
    }

    /**
     * Ustawia identyfikator sesji
     * zwraca ustawiony identyfikator
     * @param string $id identyfikator
     * @return string
     */
    public static function setId($id)
    {
        return session_id($id);
    }

    /**
     * Pobiera identyfikator sesji
     * @return string
     */
    public static function getId()
    {
        return session_id();
    }

    /**
     * Pobiera przekształcony do integera identyfikator sesji
     * @return int
     */
    public static function getNumericId()
    {
        $hashId = self::getId();
        $id = (integer) substr(preg_replace('/[a-z]+/', '', $hashId), 0, 9);
        $letters = preg_replace('/[0-9]+/', '', $hashId);
        for ($i = 0, $length = strlen($letters); $i < $length; $i++) {
            $id += ord($letters[$i]) - 97;
        }
        return $id;
    }

    /**
     * Niszczy sesję
     * @return boolean
     */
    public static function destroy()
    {
        return session_destroy();
    }

    /**
     * Regeneruje identyfikator sesji
     * kopiuje dane starej sesji do nowej
     * @param boolean $deleteOldSession kasuje starą sesję
     * @return boolean
     */
    public static function regenerateId($deleteOldSession = true)
    {
        return session_regenerate_id($deleteOldSession);
    }

}
