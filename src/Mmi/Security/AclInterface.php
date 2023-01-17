<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

interface AclInterface
{
    /**
     * Adds resource
     */
    public function add(string $resource): self;

    /**
     * Checks resource existence
     */
    public function has(string $resource): bool;

    /**
     * Adds role
     */
    public function addRole(string $role): self;

    /**
     * Checks role existence
     */
    public function hasRole(string $role): bool;

    /**
     * Allows role on a resource
     */
    public function allow(string $role, string $resource): self;

    /**
     * Denies role on a resource
     */
    public function deny(string $role, string $resource): self;

    /**
     * Checks if role array allows a resource
     */
    public function isAllowed(array $roles, string $resource): bool;

    /**
     * Sprawdza dostęp roli do zasobu
     */
    public function isRoleAllowed(string $role, string $resource): bool;

    /**
     * Zwracanie ról
     */
    public function getRoles(): array;
}
