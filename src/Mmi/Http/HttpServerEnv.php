<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

/**
 * Klasa środowiska serwera HTTP
 * 
 * @property string $applicationLanguage do ustawienia jako zmienna serwera dla stron wielojęzycznych
 * @property string $requestUri uri żądania HTTP
 * @property string $baseUrl ścieżka bazowa aplikacji
 * @property string $authUser autoryzowany użytkownik basic-auth
 * @property string $authPassword hasło użytkownika basic-auth
 * @property string $scriptFilename wykonywany plik (wejście do aplikacji)
 *  
 * @property string $remoteAddress ip klienta
 * @property string $remotePort port klienta
 *
 * @property string $serverAddress adres serwera
 * @property string $serverPort port serwera
 * @property string $serverSoftware oprogramowanie serwera
 *  
 * @property string $httpAcceptLanguage Http-Accept-Language
 * @property string $httpAcceptEncoding Http-Accept-Encoding
 * @property string $httpHost host HTTP
 * @property string $httpReferer referer HTTP
 * @property bool $httpSecure czy połączenie SSL
 * @property string $httpUserAgent przeglądarka
 * @property string $httpRange zakres bajtów pliku
 */
class HttpServerEnv extends \Mmi\DataObject
{

    /**
     * Konstruktor
     */
    public function __construct()
    {
        //x-forwarded-for
        $xForwarderFor = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_SPECIAL_CHARS);
        //ustawianie zmiennych środowiskowych
        $this->_data = [
            'authUser' => isset($_SERVER['PHP_AUTH_USER']) ? filter_var($_SERVER['PHP_AUTH_USER']) : null,
            'authPassword' => isset($_SERVER['PHP_AUTH_PW']) ? filter_var($_SERVER['PHP_AUTH_PW']) : null,
            'applicationLanguage' => filter_input(INPUT_SERVER, 'APPLICATION_LANGUAGE'),
            'scriptFileName' => filter_input(INPUT_SERVER, 'SCRIPT_FILENAME', FILTER_SANITIZE_SPECIAL_CHARS),
            'remoteAddress' => $xForwarderFor ? $xForwarderFor : filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_SPECIAL_CHARS),
            'remotePort' => filter_input(INPUT_SERVER, 'REMOTE_PORT', FILTER_SANITIZE_NUMBER_INT),
            'serverAddress' => filter_input(INPUT_SERVER, 'SERVER_ADDR', FILTER_SANITIZE_SPECIAL_CHARS),
            'serverPort' => filter_input(INPUT_SERVER, 'SERVER_PORT', FILTER_SANITIZE_NUMBER_INT),
            'serverSoftware' => filter_input(INPUT_SERVER, 'SERVER_SOFTWARE', FILTER_SANITIZE_SPECIAL_CHARS),
            'httpSecure' => ('on' == (filter_input(INPUT_SERVER, 'HTTPS')) || ('https' == filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_PROTO')) || 443 == $this->serverPort) ? true : false,
            'httpAcceptLanguage' => filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE', FILTER_SANITIZE_SPECIAL_CHARS),
            'httpAcceptEncoding' => filter_input(INPUT_SERVER, 'HTTP_ACCEPT_ENCODING', FILTER_SANITIZE_SPECIAL_CHARS),
            'httpHost' => filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_SPECIAL_CHARS),
            'httpReferer' => filter_input(INPUT_SERVER, 'HTTP_REFERER', FILTER_SANITIZE_STRING),
            'httpUserAgent' => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_SPECIAL_CHARS),
            'httpRange' => filter_input(INPUT_SERVER, 'HTTP_RANGE', FILTER_SANITIZE_SPECIAL_CHARS),
        ];
        //dekodowanie url i zastąpienie
        if (null === $this->_data['requestUri'] = str_replace(['&amp;', '&#38;'], '&', trim(urldecode(filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_SPECIAL_CHARS)), '/'))) {
            return;
        }
        //obsługa serwisu w podkatalogu
        $subFolderPath = substr(BASE_PATH, strrpos(BASE_PATH, '/') + 1) . '/web';
        $position = strpos($this->_data['requestUri'], $subFolderPath);
        if (false !== $position) {
            $this->_data['baseUrl'] = '/' . substr($this->_data['requestUri'], 0, strlen($subFolderPath) + $position);
            $this->_data['requestUri'] = trim(substr($this->_data['requestUri'], strlen($subFolderPath) + $position + 1), '/');
        }
        //wejście przez plik PHP
        if ($this->_data['requestUri'] && (false !== $scriptPosition = strpos($this->_data['requestUri'], $fileName = basename($this->scriptFileName)))) {
            $this->_data['requestUri'] = substr($this->_url, $scriptPosition + strlen($fileName) + 1);
        }
        $this->_data['requestUri'] = rtrim($this->_data['requestUri'], '/');
    }

    /**
     * Zablokowany setter
     * @param string $key
     * @param mixed $value
     * @throws HttpException
     */
    public function __set($key, $value)
    {
        throw new HttpException('Unable to ser environment variable: ' . $key);
    }

}
