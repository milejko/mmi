<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Security;

use Mmi\Security\Auth;

class AuthTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $auth = new Auth;
        //brak modelu
        $this->assertFalse($auth->authenticate());
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth->setModelName('\Mmi\Test\Model\AuthModel')
                ->setSalt('salt')
                ->setIdentity('test')
                ->setCredential('test1'));
        $this->assertFalse($auth->authenticate());
        $this->assertFalse($auth->hasIdentity());
        $auth->setCredential('test');
        $this->assertTrue($auth->authenticate());
        $this->assertTrue($auth->hasIdentity());
        $this->assertEquals(1, $auth->getId());
        $this->assertEquals('test@example.com', $auth->getEmail());
        $this->assertTrue($auth->hasRole('member'));
        $this->assertEquals(['member'], $auth->getRoles());
        $this->assertNull($auth->getName());
        $this->assertEquals('test', $auth->getUsername());
        $this->assertEquals('salt', $auth->getSalt());
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth->clearIdentity());
        $this->assertFalse($auth->hasIdentity());
        $this->assertNull($auth->getId());
        $auth->setIdentity(7);
        $this->assertTrue($auth->idAuthenticate());
        $this->assertTrue($auth->hasIdentity());
        $this->assertEquals(1, $auth->getId());
        $this->assertEquals('test@example.com', $auth->getEmail());
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth->clearIdentity());
        $this->assertFalse($auth->idAuthenticate());
        $this->assertInstanceOf('\Mmi\Session\SessionSpace', $auth->getSessionNamespace());
        $this->assertEmpty($auth->getData());
    }

    /**
     * @expectedException \Mmi\Security\SecurityException
     */
    public function testAuthenticate()
    {
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth = (new Auth)
            ->setModelName('\Mmi\Test\Model\AuthModel')
            ->setIdentity('fake'));
        $auth->authenticate();
    }

    /**
     * @expectedException \Mmi\Security\SecurityException
     */
    public function testIdAuthenticate()
    {
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth = (new Auth)
            ->setModelName('\Mmi\Test\Model\AuthModel')
            ->setIdentity('fake'));
        $auth->idAuthenticate();
    }

    /**
     * @expectedException \Mmi\Security\SecurityException
     */
    public function testNoSalt()
    {
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth = new Auth);
        $auth->getSalt();
    }

}
