<?php

declare(strict_types=1);


namespace MatchmakerTests\Acceptance;


use AcceptanceTester;


class LoginCest
{
    public function loginExistingUser(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->haveInDatabase(
            'users',
            [
                'username' => 'go',
                'secret' => password_hash('test', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
            ]
        );

        $I->amGoingTo("log in with a password");
        $I->seeLink('Log In', '/login');
        $I->click('Log In');

        $I->expectTo("be in the login page");
        $I->seeCurrentUrlEquals('/login');

        $I->expectTo("have a login form");
        $I->seeElement('form input[id=username]');
        $I->seeElement('form input[id=password]');
        $I->see('Username');
        $I->see('Password');

        $I->expectTo("be able to log in");
        $I->fillField('username', 'go');
        $I->fillField('password', 'test');
        $I->click('Login');

        $I->expectTo("be logged in on the home page");
        $I->seeCurrentUrlEquals('/');
        $I->dontSeeLink('Log In');
        $I->seeLink('go', '/logout');

        $I->expectTo("be able to log out");
        $I->click('go');

        $I->expectTo("be logged out");
        $I->seeCurrentUrlEquals('/');
        $I->dontSeeLink('go', '/logout');
        $I->seeLink('Log In', '/login');
    }
}
