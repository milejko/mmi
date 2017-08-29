<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

class Acl
{

    /**
     * Zasoby
     * @var array
     */
    private $_resources = [];

    /**
     * Role
     * @var array
     */
    private $_roles = [];

    /**
     * Uprawnienia
     * @var array
     */
    private $_rights = [];

    /**
     * Dodaje zasób
     * @param string $resource zasób
     * @return \Mmi\Security\Acl
     */
    public function add($resource)
    {
        $this->_resources[strtolower($resource)] = true;
        return $this;
    }

    /**
     * Sprawdza istnienie zasobu
     * @param string $resource zasób
     * @return boolean
     */
    public function has($resource)
    {
        return isset($this->_resources[strtolower($resource)]);
    }

    /**
     * Dodaje rolę
     * @param string $role rola
     * @return \Mmi\Security\Acl
     */
    public function addRole($role)
    {
        $this->_roles[$role] = true;
        return $this;
    }

    /**
     * Sprawdza istnienie roli
     * @param string $role rola
     * @return boolean
     */
    public function hasRole($role)
    {
        return isset($this->_roles[$role]);
    }

    /**
     * Ustawia pozwolenie na dostęp roli do zasobu
     * @param string $role rola
     * @param string $resource zasób
     * @return \Mmi\Security\Acl
     */
    public function allow($role, $resource)
    {
        //dodawanie roli i zasobu
        $this->add($resource)->addRole($role);
        $this->_rights[$role . ':' . strtolower($resource)] = true;
        return $this;
    }

    /**
     * Ustawia zakaz dostępu roli do zasobu
     * @param string $role rola
     * @param string $resource zasób
     * @return \Mmi\Security\Acl
     */
    public function deny($role, $resource)
    {
        //dodawanie roli i zasobu
        $this->add($resource)->addRole($role);
        $this->_rights[$role . ':' . strtolower($resource)] = false;
        return $this;
    }

    /**
     * Sprawdza dostęp grupy ról do zasobu
     * @param array $roles tablica ról
     * @param string $resource zasób
     * @return boolean
     */
    public function isAllowed(array $roles, $resource)
    {
        $allowed = false;
        foreach ($roles as $role) {
            if ($this->isRoleAllowed($role, $resource)) {
                $allowed = true;
                break;
            }
        }
        return $allowed;
    }

    /**
     * Sprawdza dostęp roli do zasobu
     * @param string $role rola
     * @param string $resource zasób
     * @return boolean
     */
    public function isRoleAllowed($role, $resource)
    {
        //zmniejszanie liter
        $resource = strtolower($resource);
        //istnieje konkretne uprawnienie
        if (isset($this->_rights[$role . ':' . $resource])) {
            return $this->_rights[$role . ':' . $resource];
        }
        //uprawnienie do zasobu powyżej
        if (strrpos($resource, ':') !== false) {
            return $this->isRoleAllowed($role, substr($resource, 0, strrpos($resource, ':')));
        }
        //globalne dla roli
        if (isset($this->_rights[$role . ':'])) {
            return $this->_rights[$role . ':'];
        }
        return false;
    }

}
