<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

use Mmi\Http\Response;

/**
 * Klasa autoryzacji
 */
class Auth implements AuthInterface
{

    /**
     * Przestrzeń nazw w sesji przeznaczona dla autoryzacji
     * @var string
     */
    const SESSION_NAMESPACE = 'Auth';

    /**
     * Przestrzeń w sesji
     * @var \Mmi\Session\SessionSpace
     */
    private $_session;

    /**
     * Identyfikator użytkownika (np. login)
     * @var string
     */
    private $_identity;

    /**
     * Ciąg uwierzytelniający (np. hasło)
     * @var string
     */
    private $_credential;

    /**
     * @var AuthProviderInterface
     */
    private $authProvider;

    /**
     * Kostruktor, tworzy przestrzeń w sesji
     */
    public function __construct(AuthProviderInterface $authProvider)
    {
        //otwieranie przestrzeni w sesji
        $this->_session     = new \Mmi\Session\SessionSpace(self::SESSION_NAMESPACE);
        $this->authProvider = $authProvider;
    }

    /**
     * Sprawdza czy użytkownik posiada tożsamość
     */
    public function hasIdentity(): bool
    {
        //brak tożsamości
        return (bool)$this->_session->id;
    }

    /**
     * Pobiera rolę
     */
    public function getRoles(): array
    {
        return isset($this->_session->roles) ? $this->_session->roles : ['guest'];
    }

    /**
     * Sprawdza istnienie roli
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * Pobiera pełna nazwę zalgowanego użytkownika
     */
    public function getName(): string
    {
        return (string) $this->_session->name;
    }

    /**
     * Pobiera nazwę zalgowanego użytkownika
     */
    public function getUsername(): string
    {
        return (string) $this->_session->username;
    }

    /**
     * Pobiera email zalogowanego użytkownika
     */
    public function getEmail(): string
    {
        return (string) $this->_session->email;
    }

    /**
     * Zwraca dane zalogowanego użytkownika
     * @return mixed
     */
    public function getData()
    {
        return $this->_session->data;
    }

    /**
     * Pobiera identyfikator użytkownika, lub null jeśli brak
     * @return mixed
     */
    public function getId(): string
    {
        return (string) $this->_session->id;
    }

    /**
     * Ustawia identyfikator do autoryzacji (np. login)
     */
    public function setIdentity(string $identity): self
    {
        $this->_identity = $identity;
        return $this;
    }

    /**
     * Ustawia ciąg uwierzytelniający do autoryzacji (np. hasło)
     */
    public function setCredential(string $credential): self
    {
        $this->_credential = $credential;
        return $this;
    }

    /**
     * Czyści tożsamość
     */
    public function clearIdentity(): self
    {
        //czyszczenie
        $this->_identity = $this->_credential = null;
        //wylogowanie na adapterze
        $this->authProvider->deauthenticate();
        //czyszczenie sesji
        $this->_session->unsetAll();
        return $this;
    }

    /**
     * Autoryzacja
     */
    public function authenticate(): bool
    {
        //błąd logowania na modelu
        if (null === ($record = $this->authProvider->authenticate($this->_identity, $this->_credential))) {
            return false;
        }
        //ustawia autoryzację
        return $this->_setAuthentication($record);
    }

    /**
     * Wymuszenie ustawienia autoryzacji
     */
    protected function _setAuthentication(AuthRecord $record)
    {
        //przekazanie danych z rekordu autoryzacji do sesji
        $this->_session->setFromArray((array)$record);
        return true;
    }

    /**
     * Zaufana autoryzacja
     */
    public function idAuthenticate(): bool
    {
        //autoryzacja po ID w modelu nieudana
        if (null === $record = $this->authProvider->idAuthenticate($this->_identity)) {
            return false;
        }
        //ustawia autoryzację
        return $this->_setAuthentication($record);
    }

    /**
     * Uwierzytelnienie przez http
     */
    public function httpAuth($user, $password, Response $response, $realm = '', $errorMessage = '')
    {
        //pobieranie usera i hasła ze zmiennych środowiskowych
        $this->setIdentity($user)
            ->setCredential($password);
        //autoryzacja poprawna
        if (null !== $this->authProvider->authenticate($this->_identity, $this->_credential)) {
            return;
        }
        //odpowiedź 401
        $response
            ->setHeader('WWW-Authenticate', 'Basic realm="' . $realm . '"')
            ->setCodeUnauthorized()
            ->setContent($errorMessage);
        //zakończenie skryptu
        exit;
    }
}