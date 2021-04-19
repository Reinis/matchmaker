<?php

declare(strict_types=1);


namespace MatchmakerTests\Acceptance;


use AcceptanceTester;


class RegisterCest
{
    public function registerANewUser(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->dontSeeInDatabase('users', ['username' => 'go']);

        $I->amGoingTo("register a new user");
        $I->seeLink('Log In', '/login');
        $I->click('Log In');

        $I->expectTo("be on the login page");
        $I->seeCurrentUrlEquals('/login');

        $I->amGoingTo("go to the registration page");
        $I->seeLink('Register', '/register');
        $I->click('Register');

        $I->expectTo("be on the registration page");
        $I->seeCurrentUrlEquals('/register');
        $I->seeElement('form');

        $I->amGoingTo("fill the form");
        $I->fillField('username', 'go');
        $I->fillField('password', 'test');
        $I->click('Register');

        $I->expectTo("be redirected to the front page");
        $I->seeCurrentUrlEquals('/');
        $I->see("Registration successful");
    }
}
