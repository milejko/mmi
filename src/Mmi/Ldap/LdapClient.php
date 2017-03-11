<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Ldap;

/**
 * Klasa klienta LDAP
 */
class LdapClient extends \Mmi\OptionObject {

	/**
	 * Konfiguracja serwera
	 * @var \Mmi\Ldap\LdapConfig
	 */
	private $_config;

	/**
	 * Zasób aktywnego serwera
	 * @var resource
	 */
	private $_activeServerResource;

	/**
	 * Konstruktor sprawdza istnienie modułu ldap
	 * @param LdapConfig $config konfiguracja serwera
	 * @throws \Mmi\Ldap\Exception brak modułu LDAP
	 */
	public function __construct(LdapConfig $config) {
		//brak modułu LDAP
		if (!\extension_loaded('ldap')) {
			throw new LdapException('LDAP extension not installed');
		}
		$this->_config = $config;
	}

	/**
	 * Destruktor zamykający połączenia
	 */
	public function __destruct() {
		$this->_close();
	}

	/**
	 * Autoryzuje po DN i haśle
	 * @param string $login login lub dn
	 * @param string $password
	 * @return boolean
	 * @throws \Mmi\Ldap\Exception błędy parametów (nie logowania)
	 */
	public function authenticate($login, $password) {
		$server = $this->_getActiveServer();
		try {
			//autoryzacja czystym loginem
			return ldap_bind($server, $login, $password);
		} catch (\Exception $e) {
			//niepoprawne dane logowania
			return false;
		}
	}

	/**
	 * Znajduje po filtrze
	 * @param string $filter
	 * @param integer $limit
	 * @param string $dn opcjonalny dn
	 * @return \Mmi\LdapUserCollection
	 */
	public function findUser($filter = '*', $limit = 100, array $searchFields = ['mail', 'cn', 'uid', 'sAMAccountname'], $dn = null) {
		//brak możliwości zalogowania
		if (!$this->authenticate($this->_config->user, $this->_config->password)) {
			throw new LdapException('Unable to find users due to "' . $this->_config->user . '" is not authorized');
		}
		try {
			//budowanie filtra
			$searchString = '(|';
			foreach ($searchFields as $field) {
				$searchString .= '(' . $field . '=' . $filter . ')';
			}
			$searchString .= ')';
			//odpowiedź z LDAP'a
			$rawResource = ldap_search($this->_getActiveServer(), $dn ? : 'dc=' . implode(',dc=', explode('.', $this->_config->domain)), $searchString);
		} catch (\Exception $e) {
			//puste
			return new LdapUserCollection;
		}
		//konwersja do obiektów
		return new LdapUserCollection(ldap_get_entries($this->_getActiveServer(), $rawResource), $limit);
	}

	/**
	 * Wybiera aktywny serwer z puli serwerów
	 * @return type
	 * @throws LdapException
	 */
	private function _getActiveServer() {
		//jeśli serwer jest już wybrany - zwrot
		if ($this->_activeServerResource) {
			return $this->_activeServerResource;
		}
		//jeśli adres nie jest tablicą - tworzy tablicę 1 elementową z nim w środku
		$servers = is_array($this->_config->address) ? $this->_config->address : [$this->_config->address];
		//logowanie kolejności serwerów
		\shuffle($servers);
		//wybór akrywnego serwera
		foreach ($servers as $address) {
			//parsowanie adresu i ping do serwera
			if (!$this->_isServerAlive($serverAddress = $this->_parseAddress($address))) {
				continue;
			}
			//powoływanie zasobu ldap
			$ldap = ldap_connect($serverAddress->protocol . '://' . $serverAddress->host, $serverAddress->port);
			//ustawianie opcji ldap
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			//zwrot zasobu
			return $this->_activeServerResource = $ldap;
		}
		//brak aktywnego serwera
		throw new LdapException('No alive LDAP server found');
	}

	/**
	 * Określa aktywność serwera
	 * @param $serverAddress adres serwera
	 * @return boolean
	 */
	private function _isServerAlive(LdapServerAddress $serverAddress) {
		try {
			$errno = null;
			$errstr = null;
			//próba połączenia i zamknięcia połączenia
			fclose(fsockopen($serverAddress->host, $serverAddress->port, $errno, $errstr, 1));
			return true;
		} catch (\Exception $e) {
			//nie można połączyć
			return false;
		}
	}

	/**
	 * Parsuje adres typu ldap://example.com:389
	 * @param string adres
	 * @return LdapServerAddress
	 */
	private function _parseAddress($address) {
		//nowy obiekt adresu
		$serverAddress = new LdapServerAddress();
		$matches = [];
		//ustawianie protokołu
		if (preg_match('/^([a-z]+):\/\//', $address, $matches)) {
			$address = str_replace($matches[1] . '://', '', $address);
			$serverAddress->protocol = $matches[1];
		}
		//błędny protokół
		if ($serverAddress->protocol != 'ldap' && $serverAddress->protocol != 'ldaps') {
			throw new LdapException('Invalid server protocol: ' . $serverAddress->protocol);
		}
		//ustalanie portu
		if (preg_match('/:([0-9]+)$/', $address, $matches)) {
			$address = str_replace(':' . $matches[1], '', $address);
			$serverAddress->port = $matches[1];
		}
		//ustalanie hosta
		if (preg_match('/([a-z\.0-9]+)/', $address, $matches)) {
			$serverAddress->host = $matches[1];
			return $serverAddress;
		}
		//niepoprawny adres
		throw new LdapException('Invalid server address: ' . $address);
	}

	/**
	 * Zamyka połączenie z aktywnym serwerem
	 */
	private function _close() {
		//połączenie nie jest otwarte
		if (!$this->_activeServerResource) {
			return;
		}
		//zamykanie połączenia
		ldap_close($this->_activeServerResource);
		$this->_activeServerResource = null;
	}

}
