<?php

declare(strict_types=1);


namespace MatchmakerTests\Acceptance;


use AcceptanceTester;


class ImageCest
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
            ]
        );

        $I->amGoingTo("log in");
        $I->amOnPage('/login');
        $I->fillField('username', 'go');
        $I->fillField('password', 'test');
        $I->click('Login');

        $I->expectTo("succeed");
        $I->seeLink('go', '/logout');

        $I->amGoingTo("upload an image");
        $I->amOnPage('/images');
        $I->attachFile('imageFile', '../../storage/assets/Personality Pack PNG/Andy.png');
        $I->click('Upload Image');

        $I->expectTo("see the image");
        $I->seeCurrentUrlEquals('/images');
        $I->see("File 'Andy.png' uploaded successfully", '.flash');
        $I->seeElement('img', ['alt' => 'Andy.png']);
    }

    public function viewAndDeleteAnImage(AcceptanceTester $I): void
    {
        $I->amOnPage('/images');
        $I->seeElement('img', ['alt' => 'Andy.png']);

        $I->amGoingTo("click on the image");
        $I->click('Andy.png');

        $I->expectTo("see the image in a separate page");
        $I->seeCurrentUrlMatches('|/images/(\d+)|');
        $I->seeElement('img', ['alt' => 'Andy.png']);
        $I->seeLink('Back');
        $I->seeElement('form > input[value=Delete]');

        $I->amGoingTo("go back");
        $I->click('Back');

        $I->expectTo("to return to the images page");
        $I->seeCurrentUrlEquals('/images');
        $I->seeElement('img', ['alt' => 'Andy.png']);
        $I->click('Andy.png');

        $I->amGoingTo("delete the picture");
        $I->click('Delete');

        $I->expectTo("have the picture deleted");
        $I->seeCurrentUrlEquals('/images');
        $I->dontSeeElement('img', ['alt' => 'Andy.png']);
        $I->see('Image deleted successfully', '.flash');
    }
}
