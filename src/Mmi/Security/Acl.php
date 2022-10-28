<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

class Acl implements AclInterface
{
    public const SEPARATOR = ':';

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
     */
    public function add(string $resource): self
    {
        $this->_resources[strtolower($resource)] = true;
        return $this;
    }

    /**
     * Sprawdza istnienie zasobu
     */
    public function has(string $resource): bool
    {
        return isset($this->_resources[strtolower($resource)]);
    }

    /**
     * Dodaje rolę
     */
    public function addRole(string $role): self
    {
        $this->_roles[$role] = true;
        return $this;
    }

    /**
     * Sprawdza istnienie roli
     */
    public function hasRole(string $role): bool
    {
        return isset($this->_roles[$role]);
    }

    /**
     * Ustawia pozwolenie na dostęp roli do zasobu
     */
    public function allow(string $role, string $resource): self
    {
        //dodawanie roli i zasobu
        $this->add($resource)->addRole($role);
        $this->_rights[$role . self::SEPARATOR . strtolower($resource)] = true;
        return $this;
    }

    /**
     * Ustawia zakaz dostępu roli do zasobu
     */
    public function deny(string $role, string $resource): self
    {
        //dodawanie roli i zasobu
        $this->add($resource)->addRole($role);
        $this->_rights[$role . self::SEPARATOR . strtolower($resource)] = false;
        return $this;
    }

    /**
     * Sprawdza dostęp grupy ról do zasobu
     */
    public function isAllowed(array $roles, $resource): bool
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
     */
    public function isRoleAllowed(string $role, string $resource): bool
    {
        //zmniejszanie liter
        $resource = strtolower($resource);
        //istnieje konkretne uprawnienie
        if (isset($this->_rights[$role . self::SEPARATOR . $resource])) {
            return $this->_rights[$role . self::SEPARATOR . $resource];
        }
        //uprawnienie do zasobu powyżej
        if (strrpos($resource, self::SEPARATOR) !== false) {
            return $this->isRoleAllowed($role, substr($resource, 0, strrpos($resource, self::SEPARATOR)));
        }
        //globalne dla roli
        if (isset($this->_rights[$role . self::SEPARATOR])) {
            return $this->_rights[$role . self::SEPARATOR];
        }
        return false;
    }
}
