<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
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
     */
    public function add($resource)
    {
        $this->_resources[strtolower($resource)] = true;
    }

    /**
     * Sprawdza istnienie zasobu
     * @param string $resource zasób
     */
    public function has($resource)
    {
        return isset($this->_resources[strtolower($resource)]);
    }

    /**
     * Dodaje rolę
     * @param string $role rola
     */
    public function addRole($role)
    {
        if (!isset($this->_roles[$role])) {
            $this->_roles[$role] = true;
        }
    }

    /**
     * Sprawdza istnienie roli
     * @param string $role rola
     */
    public function hasRole($role)
    {
        return isset($this->_roles[$role]);
    }

    /**
     * Ustawia pozwolenie na dostęp roli do zasobu
     * @param string $role rola
     * @param string $resource zasób
     */
    public function allow($role, $resource)
    {
        $this->addRole($role);
        $this->_rights[$role . ':' . strtolower($resource)] = true;
    }

    /**
     * Ustawia zakaz dostępu roli do zasobu
     * @param string $role rola
     * @param string $resource zasób
     */
    public function deny($role, $resource)
    {
        $this->addRole($role);
        $this->_rights[$role . ':' . strtolower($resource)] = false;
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
