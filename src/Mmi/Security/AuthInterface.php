<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

/**
 * Klasa autoryzacji
 */
interface AuthInterface
{

    /**
     * Sprawdza czy użytkownik posiada tożsamość
     * @return boolean
     */
    public function hasIdentity(): bool;

    /**
     * Pobiera rolę
     * @return array
     */
    public function getRoles(): array;

    /**
     * Sprawdza istnienie roli
     * @param string $role rola
     * @return boolean
     */
    public function hasRole(string $role): bool;

    /**
     * Pobiera pełna nazwę zalgowanego użytkownika
     * @return string
     */
    public function getName(): string;

    /**
     * Pobiera nazwę zalgowanego użytkownika
     * @return string
     */
    public function getUsername(): string;

    /**
     * Pobiera email zalogowanego użytkownika
     * @return string
     */
    public function getEmail(): string;

    /**
     * Zwraca dane zalogowanego użytkownika
     * @return mixed
     */
    public function getData();

    /**
     * Pobiera identyfikator użytkownika, lub null jeśli brak
     * @return mixed
     */
    public function getId();

    /**
     * Ustawia identyfikator do autoryzacji (np. login)
     */
    public function setIdentity(string $identity): self;

    /**
     * Ustawia ciąg uwierzytelniający do autoryzacji (np. hasło)
     */
    public function setCredential(string $credential): self;

    /**
     * Czyści tożsamość
     * @param boolean $cookies czyści także ciastka zapamiętujące użytkownika
     * @return \Mmi\Security\Auth
     */
    public function clearIdentity(): self;

    /**
     * Autoryzacja
     * @return boolean
     */
    public function authenticate(): bool;

    /**
     * Zaufana autoryzacja
     * @return boolean
     */
    public function idAuthenticate(): bool;

}
