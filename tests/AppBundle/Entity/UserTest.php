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

    public function testGetId()
    {
        $this->assertNull($this->user->getId());
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

    public function testSetAndGetLastName()
    {
        $this->user->setLastName('Dupont');
        $this->assertEquals('Dupont', $this->user->getLastName());
        $this->assertNotNull($this->user->getLastName());
    }

    public function testSetAndGetGender()
    {
        $this->user->setGender('Homme');
        $this->assertEquals('Homme', $this->user->getGender());
        $this->assertNotNull($this->user->getGender());
    }

    public function testSetAndGetEmail()
    {
        $this->user->setEmail('johndupont@email.com');
        $this->assertEquals('johndupont@email.com', $this->user->getEmail());
        $this->assertNotNull($this->user->getEmail());
    }

    public function testSetAndGetBirthDate()
    {
        $this->user->setBirthDate(new \DateTime('1990-01-10'));
        $this->assertEquals(new \DateTime('1990-01-10'), $this->user->getBirthDate());
        $this->assertNotNull($this->user->getBirthDate());
    }

    public function testSetAndGetAddress()
    {
        $this->user->setAddress('4th Street');
        $this->assertEquals('4th Street', $this->user->getAddress());
        $this->assertNotNull($this->user->getAddress());
    }

    public function testSetAndGetPostCode()
    {
        $this->user->setPostCode('30301');
        $this->assertEquals('30301', $this->user->getPostCode());
        $this->assertNotNull($this->user->getPostCode());
    }

    public function testSetAndGetCity()
    {
        $this->user->setCity('Atlanta');
        $this->assertEquals('Atlanta', $this->user->getCity());
        $this->assertNotNull($this->user->getCity());
    }

    public function testGetSalt()
    {
        $this->assertEquals(null, $this->user->getSalt());
    }

    public function testGetUsername()
    {
        $this->assertEquals($this->user->getLogin(), $this->user->getUsername());
    }

    public function testGetRoles()
    {
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }
}
