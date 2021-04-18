<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use Matchmaker\Entities\User;
use UnitTester;


class UserTest extends Unit
{
    protected UnitTester $tester;

    public function testNewUser(): User
    {
        $user = new User(
            'Jonny',
            '***',
            'John',
            'Doe',
            'male',
        );

        self::assertEquals(null, $user->getId());
        self::assertEquals('John', $user->getFirstName());
        self::assertEquals('Doe', $user->getLastName());
        self::assertEquals('male', $user->getGender());
        self::assertEquals('Default', $user->getProfilePic());

        return $user;
    }

    /**
     * @depends testNewUser
     */
    public function testGetName(User $user): void
    {
        self::assertEquals('John Doe', $user->getName());
    }

    /**
     * @depends testNewUser
     */
    public function testSetFirstName(User $user): void
    {
        $user->setFirstName('Jane');

        self::assertEquals('Jane', $user->getFirstName());
        self::assertEquals('Jane Doe', $user->getName());
    }

    /**
     * @depends testNewUser
     */
    public function testSetLastName(User $user): void
    {
        $user->setLastName('Snu');

        self::assertEquals('Snu', $user->getLastName());
        self::assertEquals('Jane Snu', $user->getName());
    }

    /**
     * @depends testNewUser
     */
    public function testSetGender(User $user): void
    {
        $user->setGender('female');

        self::assertEquals('female', $user->getGender());
    }

    /**
     * @depends testNewUser
     */
    public function testSetProfilePic(User $user): void
    {
        $user->setProfilePic('Other Picture.png');

        self::assertEquals('Other Picture.png', $user->getProfilePic());
    }
}
