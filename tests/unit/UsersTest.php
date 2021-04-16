<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\User;
use UnitTester;


class UsersTest extends Unit
{
    protected UnitTester $tester;

    public function testNewUsers(): void
    {
        $users = new Users(
            new User('John', 'Doe', 'male'),
            new User('Jane', 'Snu', 'female'),
        );

        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertContainsEquals($user->getFirstName(), ['John', 'Jane']);
            self::assertContainsEquals($user->getLastName(), ['Doe', 'Snu']);
            self::assertContainsEquals($user->getGender(), ['male', 'female']);
        }
    }
}
