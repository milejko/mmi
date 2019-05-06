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
    /**
     * @var array
     */
    private $config;
    
    /**
     * Session constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    /**
     * Rozpoczęcie sesji
     */
    public function start()
    {
        session_name($this->config['name']);
        session_set_cookie_params(
            $this->config['cookie_lifetime'],
            $this->config['cookie_path'],
            $this->config['cookie_domain'],
            $this->config['cookie_secure'],
            $this->config['cookie_http_only']
        );
        session_cache_expire($this->config['cache_expire']);
        ini_set('session.gc_divisor', $this->config['gc_divisor']);
        ini_set('session.gc_maxlifetime', $this->config['gc_max_lifetime']);
        ini_set('session.gc_probability', $this->config['gc_probability']);
        if ($this->config['handler'] == 'user') {
            $handlerClass = $this->config['path'];
            $handler = new $handlerClass();
            session_set_save_handler($handler);
        } else {
            ini_set('session.save_handler', $this->config['handler']);
            session_save_path($this->config['path']);
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
