<?php

declare(strict_types=1);


namespace MatchmakerTests\Acceptance;


use AcceptanceTester;


class UploadCest
{
    public function _before(AcceptanceTester $I): void
    {
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

        $I->amGoingTo("log in");
        $I->amOnPage('/');
        $I->click('Log In');
        $I->fillField('username', 'go');
        $I->fillField('password', 'test');
        $I->click('Login');
    }

    public function uploadAPicture(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->dontSeeLink('Log In', '/login');
        $I->seeLink('go', '/logout');

        $I->amGoingTo("upload a picture");
        $I->amOnPage('/images');
        $I->attachFile('imageFile', '../../storage/assets/Personality Pack PNG/Andy.png');
        $I->click('Upload Image');

        $I->expectTo("have the image uploaded");
        $I->seeInDatabase('pictures', ['original_name' => 'Andy.png']);
        $I->seeElement('.flash');
        $I->see("File 'Andy.png' uploaded successfully");
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->amGoingTo("delete the user");
        $I->sendAjaxPostRequest('/users/delete', ['delete_account' => 'Delete Account']);
        $I->dontSeeInDatabase('users', ['username' => 'go']);
    }
}
