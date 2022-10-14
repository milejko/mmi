<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Security;

use Mmi\Security\Auth;
use Mmi\Test\TestAuthProvider;

class AuthTest extends \PHPUnit\Framework\TestCase
{

    public function testClass()
    {
        $auth = new Auth(new TestAuthProvider);
        //brak modelu
        $this->assertFalse($auth->authenticate());
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth
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
        $this->assertEmpty($auth->getName());
        $this->assertEquals('test', $auth->getUsername());
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth->clearIdentity());
        $this->assertFalse($auth->hasIdentity());
        $this->assertEmpty($auth->getId());
        $auth->setIdentity(7);
        $this->assertTrue($auth->idAuthenticate());
        $this->assertTrue($auth->hasIdentity());
        $this->assertEquals(1, $auth->getId());
        $this->assertEquals('test@example.com', $auth->getEmail());
        $this->assertInstanceOf('\Mmi\Security\Auth', $auth->clearIdentity());
        $this->assertFalse($auth->idAuthenticate());
        $this->assertEmpty($auth->getData());
    }

}
