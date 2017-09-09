<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    protected function setUp()
    {
        $this->user = new User();
    }

    public function testGetAndSetLogin()
    {
        $this->user->setLogin('username');
        $this->assertEquals('username', $this->user->getLogin());
        $this->assertNotNull($this->user->getLogin());
    }

    public function testSetAndGetPlainPassword()
    {
        $this->user->setPlainPassword('1234');
        $this->assertEquals('1234', $this->user->getPlainPassword());
        $this->assertNotNull($this->user->getPlainPassword());
    }

    public function testSetAndGetPassword()
    {
        $this->user->setPassword('encodedPass');
        $this->assertEquals('encodedPass', $this->user->getPassword());
        $this->assertNotNull($this->user->getPassword());
    }

    public function testSetAndGetFirstName()
    {
        $this->user->setFirstName('John');
        $this->assertEquals('John', $this->user->getFirstName());
        $this->assertNotNull($this->user->getFirstName());
    }
}
