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
 * @property string $lang do ustawienia jako zmienna serwera dla stron wielojęzycznych
 * @property string $requestUri uri żądania HTTP
 * @property string $requestMethod metoda żądania
 * @property string $contentType typ treści
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
        $xForwarderFor = $this->_filter('HTTP_X_FORWARDED_FOR');
        //ustawianie zmiennych środowiskowych
        $this->_data = [
            'lang' => $this->_filter('APPLICATION_LANGUAGE'),
            'authUser' => $this->_filter('PHP_AUTH_USER'),
            'authPassword' => $this->_filter('PHP_AUTH_PW'),
            'baseUrl' => '',
            'requestMethod' => $this->_filter('REQUEST_METHOD'),
            'contentType' => $this->_filter('CONTENT_TYPE'),
            'scriptFileName' => $this->_filter('SCRIPT_FILENAME'),
            'remoteAddress' => $xForwarderFor ? $xForwarderFor : $this->_filter('REMOTE_ADDR'),
            'remotePort' => $this->_filter('REMOTE_PORT', FILTER_SANITIZE_NUMBER_INT),
            'serverAddress' => $this->_filter('SERVER_ADDR'),
            'serverPort' => $this->_filter('SERVER_PORT', FILTER_SANITIZE_NUMBER_INT),
            'httpSecure' => ('on' == ($this->_filter('HTTPS')) || ('https' == $this->_filter('HTTP_X_FORWARDED_PROTO')) || 443 == $this->serverPort) ? true : false,
            'httpAcceptLanguage' => $this->_filter('HTTP_ACCEPT_LANGUAGE'),
            'httpAcceptEncoding' => $this->_filter('HTTP_ACCEPT_ENCODING'),
            'httpHost' => $this->_filter('HTTP_HOST'),
            'httpReferer' => $this->_filter('HTTP_REFERER', FILTER_SANITIZE_STRING),
            'httpUserAgent' => $this->_filter('HTTP_USER_AGENT'),
            'httpRange' => $this->_filter('HTTP_RANGE'),
        ];
        //dekodowanie url
        if (null === $this->_data['requestUri'] = str_replace(['&amp;', '&#38;'], '&', trim($this->_filter('REQUEST_URI'), '/'))) {
            return;
        }
        //PHP_SELF wskazuje na aplikację w podkatalogu
        if (null === $newBaseUrl = preg_replace('/\/app([_a-z]+)?\.php/i', '', $this->_filter('PHP_SELF'))) {
            return;
        }
        //nadpisanie zmiennych po wykryciu apki w podkatalogu
        $this->_data['baseUrl'] = $newBaseUrl;
        $this->_data['requestUri'] = substr($this->_data['requestUri'], strlen($newBaseUrl));
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

    /**
     * Metoda filtrująca zmienną z tablicy $_SERVER
     * @param $name nazwa zmiennej
     * @param int $filter typ filtra, domyślny FILTER_SANITIZE_SPECIAL_CHARS
     * @return mixed|null
     */
    private function _filter($name, $filter = FILTER_SANITIZE_SPECIAL_CHARS)
    {
        return isset($_SERVER[$name]) ? filter_var($_SERVER[$name], $filter) : null;
    }

}
