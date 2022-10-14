<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Security;

use Mmi\Security\Acl;

class AclTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @return Acl
     */
    public function testAdd()
    {
        $this->assertInstanceOf('\Mmi\Security\Acl', $acl = (new Acl)
            ->add('god-content')
            ->add('news:user')
            ->add('news:admin')
            ->add('news:guest'));
        return $acl;
    }

    /**
     * @depends testAdd
     */
    public function testHas(Acl $acl)
    {
        $this->assertTrue($acl->has('news:user'));
        $this->assertFalse($acl->has('news'));
    }

    /**
     * @depends testAdd
     */
    public function testAddRole(Acl $acl)
    {
        $this->assertInstanceOf('\Mmi\Security\Acl', $acl->addRole('guest')
                ->addRole('admin')
                ->addRole('member'));
        return $acl;
    }

    /**
     * @depends testAddRole
     */
    public function testHasRole(Acl $acl)
    {
        $this->assertTrue($acl->hasRole('admin'));
        $this->assertTrue($acl->hasRole('guest'));
        $this->assertFalse($acl->hasRole('nonexistent'));
    }

    /**
     * @depends testAddRole
     */
    public function testAllowDeny(Acl $acl)
    {
        $this->assertInstanceOf('\Mmi\Security\Acl', $acl->allow('admin', 'news')
                ->allow('god', '')
                ->allow('member', 'news')
                ->deny('member', 'news:admin')
                ->allow('guest', 'news:guest')
        );
        return $acl;
    }

    /**
     * @depends testAllowDeny
     */
    public function testIsAllowed(Acl $acl)
    {
        $this->assertTrue($acl->isAllowed(['admin', 'guest'], 'news:admin'));
        $this->assertTrue($acl->isAllowed(['admin'], 'news:admin'));
        $this->assertTrue($acl->isAllowed(['admin'], 'news'));
        $this->assertTrue($acl->isAllowed(['admin'], 'news:user'));
        $this->assertTrue($acl->isAllowed(['god'], 'god-content'));
        $this->assertTrue($acl->isAllowed(['god'], 'news'));
        $this->assertFalse($acl->isAllowed(['guest'], 'news:admin'));
        $this->assertFalse($acl->isAllowed(['guest'], 'news'));
        $this->assertFalse($acl->isAllowed(['guest'], 'god-content'));
        $this->assertFalse($acl->isAllowed(['member'], 'god-content'));
        $this->assertFalse($acl->isAllowed(['member'], 'news:admin'));
    }

}
