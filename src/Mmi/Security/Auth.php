<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Klasa autoryzacji
 */
class Auth {

	/**
	 * Przestrzeń nazw w sesji przeznaczona dla autoryzacji
	 * @var string
	 */
	private $_namespace = 'Auth';

	/**
	 * Nazwa modelu
	 * @var string
	 */
	private $_modelName;

	/**
	 * Przestrzeń w sesji
	 * @var \Mmi\Session\Space
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
	 * Sól (unikalny dla każdej aplikacji)
	 * @var string
	 */
	private $_salt;

	/**
	 * Kostruktor, tworzy przestrzeń w sesji
	 */
	public function __construct() {
		$this->_session = new \Mmi\Session\Space($this->_namespace);
	}

	/**
	 * Ustawia sól
	 * @param string $salt
	 * @return \Mmi\Security\Auth
	 */
	public function setSalt($salt) {
		$this->_salt = $salt;
		return $this;
	}

	/**
	 * Zwraca sól
	 * @return string
	 * @throws Exception
	 */
	public function getSalt() {
		if ($this->_salt === null) {
			throw new\Exception('Salt not set, set the proper salt.');
		}
		return $this->_salt;
	}

	/**
	 * Pozwala automatycznie zalogować użytkownika przez dany czas
	 * @param int $time
	 */
	public function rememberMe($time) {
		if ($this->hasIdentity()) {
			//ustawianie ciasteczka
			new \Mmi\Http\Cookie('remember', 'id=' . $this->getId() . '&key=' . md5($this->getSalt() . $this->getId()), null, time() + $time);
		}
	}

	/**
	 * Usuwa pamięć o automatycznym logowaniu użytkownika
	 * @return \Mmi\Security\Auth
	 */
	public function forgetMe() {
		//usuwanie ciasteczka
		$cookie = new \Mmi\Http\Cookie();
		$cookie->match('remember');
		$cookie->delete();
		return $this;
	}

	/**
	 * Sprawdza czy użytkownik posiada tożsamość
	 * @return boolean
	 */
	public function hasIdentity() {
		//brak tożsamości
		if (!$this->_session->id) {
			return false;
		}
		return true;
	}

	/**
	 * Pobiera rolę
	 * @return string
	 */
	public function getRoles() {
		return isset($this->_session->roles) ? $this->_session->roles : ['guest'];
	}

	/**
	 * Sprawdza istnienie roli
	 * @param string $role rola
	 * @return boolean
	 */
	public function hasRole($role) {
		return in_array($role, $this->getRoles());
	}

	/**
	 * Pobiera pełna nazwę zalgowanego użytkownika
	 * @return string
	 */
	public function getName() {
		return $this->_session->name;
	}

	/**
	 * Pobiera nazwę zalgowanego użytkownika
	 * @return string
	 */
	public function getUsername() {
		return $this->_session->username;
	}

	/**
	 * Pobiera email zalogowanego użytkownika
	 * @return string
	 */
	public function getEmail() {
		return $this->_session->email;
	}

	/**
	 * Zwraca dane zalogowanego użytkownika
	 * @return mixed
	 */
	public function getData() {
		return $this->_session->data;
	}

	/**
	 * Zwraca przestrzeń w sesji
	 * @return \Mmi\Session\Space
	 */
	public function getSessionNamespace() {
		return $this->_session;
	}

	/**
	 * Ustawia nazwę modelu
	 * @param string $modelName
	 * @return \Mmi\Security\Auth
	 */
	public function setModelName($modelName) {
		$this->_modelName = $modelName;
		return $this;
	}

	/**
	 * Pobiera identyfikator użytkownika, lub null jeśli brak
	 * @return mixed
	 */
	public function getId() {
		return $this->_session->id;
	}

	/**
	 * Ustawia identyfikator do autoryzacji (np. login)
	 * @param string $identity identyfikator
	 * @return \Mmi\Security\Auth
	 */
	public function setIdentity($identity) {
		$this->_identity = $identity;
		return $this;
	}

	/**
	 * Ustawia ciąg uwierzytelniający do autoryzacji (np. hasło)
	 * @param string $credential ciąg uwierzytelniający
	 * @return \Mmi\Security\Auth
	 */
	public function setCredential($credential) {
		$this->_credential = $credential;
		return $this;
	}

	/**
	 * Czyści tożsamość
	 * @param boolean $cookies czyści także ciastka zapamiętujące użytkownika
	 * @return \Mmi\Security\Auth
	 */
	public function clearIdentity($cookies = true) {
		//usuwa ciasteczka
		if ($cookies) {
			$this->forgetMe();
		}
		//wylogowanie na modelu
		if ($this->_modelName) {
			$model = $this->_modelName;
			$model::deauthenticate();
		}
		//czyszczenie sesji
		$this->_session->unsetAll();
		return $this;
	}

	/**
	 * Autoryzacja
	 * @return boolean
	 */
	public function authenticate() {
		$model = $this->_modelName;
		if (!$this->_modelName) {
			return false;
		}
		//błąd logowania na modelu
		if (null === ($record = $model::authenticate($this->_identity, $this->_credential))) {
			return false;
		}
		//zła klasa rekordu
		if (!$record instanceof \Mmi\Security\Auth\Record) {
			throw new\Exception('Authentication record is not an instance of \Mmi\Security\Auth\Record');
		}
		//ustawia autoryzację
		return $this->_setAuthentication($record);
	}

	/**
	 * Wymuszenie ustawienia autoryzacji
	 * @param \Mmi\Security\Auth\Record $record
	 * @return boolean
	 */
	protected function _setAuthentication(\Mmi\Security\Auth\Record $record) {
		$this->_session->id = $record->id;
		$this->_session->username = $record->username;
		$this->_session->email = $record->email;
		$this->_session->name = $record->name;
		$this->_session->lang = $record->lang;
		//tablica ról
		$this->_session->roles = $record->roles;
		//dane dodatkowe
		$this->_session->data = $record->data;
		return true;
	}

	/**
	 * Zaufana autoryzacja
	 * @return boolean
	 */
	public function idAuthenticate() {
		$model = $this->_modelName;
		//autoryzacja po ID w modelu nieudana
		if (null === $record = $model::idAuthenticate($this->_identity)) {
			return false;
		}
		//błędny obiekt
		if (!$record instanceof \Mmi\Security\Auth\Record) {
			throw new \Exception('Authentication result is not an instance of \Mmi\Security\Auth\Record');
		}
		//ustawia autoryzację
		return $this->_setAuthentication($record);
	}

	/**
	 * Uwierzytelnienie przez http
	 * @param string $realm identyfikator przestrzeni chronionej
	 * @param string $errorMessage treść komunikatu zwrotnego - błędnego
	 */
	public function httpAuth($realm = '', $errorMessage = '') {
		//pobieranie usera i hasła ze zmiennych środowiskowych
		$this->setIdentity(\Mmi\App\FrontController::getInstance()->getEnvironment()->authUser)
			->setCredential(\Mmi\App\FrontController::getInstance()->getEnvironment()->authPassword);

		$model = $this->_modelName;
		$record = $model::authenticate($this->_identity, $this->_credential);
		//autoryzacja poprawna
		if (!$record) {
			return;
		}
		//odpowiedź 401
		\Mmi\App\FrontController::getInstance()->getResponse()
			->setHeader('WWW-Authenticate', 'Basic realm="' . $realm . '"')
			->setCodeForbidden()
			->setContent($errorMessage)
			->send();
		exit;
	}

}
