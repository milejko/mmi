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
 * Kolekcja użytkowników LDAP
 */
class LdapUserCollection extends \ArrayObject
{

    /**
     * Konstruktor z odpowiedzi ldapa
     * @param array $array
     * @param integer $limit
     */
    public function __construct(array $array = [], $limit = 10000)
    {
        if (!empty($array)) {
            return $this->setFromLdapResponse($array, $limit);
        }
        parent::__construct($array);
    }

    /**
     * Tworzy kolekcję na podstawie zwrotu z LDAP'a
     * @param array $ldapResponse
     * @return \Mmi\LdapUserCollection
     */
    public function setFromLdapResponse(array $ldapResponse, $limit = 10000)
    {
        $records = [];
        $i = 0;
        //iteracja po odpowiedzi ldap
        foreach ($ldapResponse as $user) {
            //brak cn lub dn
            if (!isset($user['cn'][0]) || !isset($user['dn'])) {
                continue;
            }
            //nowy rekord użytkownika
            $record = new LdapUserRecord;
            //ustawianie dn
            $record->dn = $user['dn'];
            //cn
            $record->cn = $user['cn'][0];
            $record->uid = isset($user['uid'][0]) ? $user['uid'][0] : null;
            $record->givenName = isset($user['givenname'][0]) ? $user['givenname'][0] : null;
            //login użytkownika
            $record->sAMAccountname = isset($user['samaccountname'][0]) ? $user['samaccountname'][0] : null;
            $record->sn = isset($user['sn'][0]) ? $user['sn'][0] : null;
            $record->mail = isset($user['mail'][0]) ? $user['mail'][0] : null;
            $record->memberOf = isset($user['memberof']) ? $user['memberof'] : [];
            //czyszczenie count z grup
            if (isset($record->memberOf['count'])) {
                unset($record->memberOf['count']);
            }
            $records[] = $record;
            $i++;
            //przekroczony limit wyników
            if ($i > $limit - 1) {
                break;
            }
        }
        $this->exchangeArray($records);
        return $this;
    }

}
