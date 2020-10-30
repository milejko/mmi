<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

class Session
{
    const FILE_HANDLER = 'files';

    public function __construct(SessionConfig $config)
    {
        session_name($config->name);
        session_set_cookie_params($config->cookieLifetime, $config->cookiePath, $config->cookieDomain, $config->cookieSecure, $config->cookieHttpOnly);
        session_cache_expire($config->cacheExpire);
        session_save_path($config->path);
        ini_set('session.gc_divisor', $config->gcDivisor);
        ini_set('session.gc_maxlifetime', $config->gcMaxLifetime);
        ini_set('session.gc_probability', $config->gcProbability);
        //file support
        if (self::FILE_HANDLER == $config->handler) {
            ini_set('session.save_handler', $config->handler);
            return;
        }
        $handlerClass = $config->handler;
        session_set_save_handler(new $handlerClass());
    }

    /**
     * Rozpoczęcie sesji
     * @param \Mmi\Session\SessionConfig $config
     * @throws SessionException
     */
    public function start(): void
    {
        session_start();
    }

    /**
     * Ustawia identyfikator sesji
     * zwraca ustawiony identyfikator
     * @param string $id identyfikator
     * @return string
     */
    public function setId($id): void
    {
        session_id($id);
    }

    /**
     * Pobiera identyfikator sesji
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * Pobiera przekształcony do integera identyfikator sesji
     */
    public function getNumericId(): int
    {
        $hashId = $this->getId();
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
    public function destroy(): void
    {
        session_destroy();
    }

    /**
     * Regeneruje identyfikator sesji
     * kopiuje dane starej sesji do nowej
     * @param boolean $deleteOldSession kasuje starą sesję
     */
    public function regenerateId($deleteOldSession = true): void
    {
        session_regenerate_id($deleteOldSession);
    }

}
