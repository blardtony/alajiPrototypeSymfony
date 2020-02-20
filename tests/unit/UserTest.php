<?php
namespace App\Tests;
use App\Entity\Teacher;

class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \App\Tests\UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testSavingUser()
    {
        $user = new Teacher;

        $user->setEmail('formateur@alaji.fr');
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword("123456");
        $user->setFullname('Formateur');
        $user->setMoodleId(2);

        $user->save();

        $this->assertEquals('Formateur', $user->getFullname());
        $this->assertEquals('formateur@alaji.fr', $user->getEmail());
        $this->assertEquals('["ROLE_USER"]', $user->getRoles());
        $this->assertEquals('123456', $user->getPassword());
        $this->assertEquals(2, $user->getMoodleId());
    }
}
