<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

class Session implements SessionInterface
{
    public const FILE_HANDLER = 'files';

    /**
     * Constructor depends on SessionConfig object
     */
    public function __construct(SessionConfig $config)
    {
        session_name($config->name);
        $cookieParams = [
            'lifetime' => $config->cookieLifetime,
            'path' => $config->cookiePath,
            'domain' => $config->cookieDomain,
            'secure' => $config->cookieSecure,
            'httponly' => $config->cookieHttpOnly,
        ];
        if ($config->cookieSecure) {
            $cookieParams['samesite'] = $config->cookieSameSite;
        }
        session_set_cookie_params($cookieParams);
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
     * Start session
     * @throws SessionException
     */
    public function start(): void
    {
        session_start();
    }

    /**
     * Sets session id
     */
    public function setId(string $id): void
    {
        session_id($id);
    }

    /**
     * Gets session id
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * Gets numeric projection of session id
     */
    public function getNumericId(): int
    {
        $hashId = $this->getId();
        $id = (int) substr(preg_replace('/[a-z]+/', '', $hashId), 0, 9);
        $letters = preg_replace('/[0-9]+/', '', $hashId);
        for ($i = 0, $length = strlen($letters); $i < $length; $i++) {
            $id += ord($letters[$i]) - 97;
        }
        return $id;
    }

    /**
     * Destroys session
     */
    public function destroy(): void
    {
        session_destroy();
    }

    /**
     * Regenerates session id
     */
    public function regenerateId(bool $deleteOldSession = true): void
    {
        session_regenerate_id($deleteOldSession);
    }
}
