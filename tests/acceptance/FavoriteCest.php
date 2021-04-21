<?php

declare(strict_types=1);


namespace MatchmakerTests\Acceptance;


use AcceptanceTester;


class FavoriteCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->haveInDatabase(
            'users',
            [
                'id' => 1,
                'username' => 'dude',
                'secret' => password_hash('***', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
            ]
        );
        $I->haveInDatabase(
            'users',
            [
                'id' => 2,
                'username' => 'june',
                'secret' => password_hash('***', PASSWORD_DEFAULT),
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'gender' => 'female',
            ]
        );
        $I->haveInDatabase(
            'users',
            [
                'id' => 3,
                'username' => 'duck',
                'secret' => password_hash('***', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]
        );
        $I->haveInDatabase(
            'pictures',
            [
                'id' => 1,
                'original_name' => 'dude.png',
                'storage' => 'test',
                'original_file' => 'random1',
                'resized_file' => 'randoms1',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 1,
            ]
        );
        $I->haveInDatabase(
            'pictures',
            [
                'id' => 2,
                'original_name' => 'june.png',
                'storage' => 'test',
                'original_file' => 'random2',
                'resized_file' => 'randoms2',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 2,
            ]
        );
        $I->haveInDatabase(
            'pictures',
            [
                'id' => 3,
                'original_name' => 'duck.png',
                'storage' => 'test',
                'original_file' => 'random3',
                'resized_file' => 'randoms3',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 3,
            ]
        );
        $I->updateInDatabase(
            'users',
            ['profile_pic' => 1],
            ['id' => 1],
        );
        $I->updateInDatabase(
            'users',
            ['profile_pic' => 2],
            ['id' => 2],
        );
        $I->updateInDatabase(
            'users',
            ['profile_pic' => 3],
            ['id' => 3],
        );
    }

    public function favoriteJune(AcceptanceTester $I): void
    {
        $I->amGoingTo("log in as dude");
        $I->amOnPage('/login');
        $I->fillField('username', 'dude');
        $I->fillField('password', '***');
        $I->click('Login');

        $I->expectTo("succeed");
        $I->seeLink('Profile', '/profile');
        $I->seeNumberOfElements('a[href^="/favorites/"]', 2);

        $I->amGoingTo("add june to my favorites");
        $I->click('a[href="/favorites/2"]');

        $I->expectTo("see a profile picture with reaction buttons");
        $I->seeElement('a[href="/"] + form');

        $I->amGoingTo("like");
        $I->click('input[formaction$="/like"]');

        $I->expectTo("return to the main page an have my like recorded");
        $I->seeInDatabase(
            'favorites',
            [
                'user_id' => 1,
                'favorite_id' => 2,
                'rating' => 1,
            ]
        );
        $I->seeCurrentUrlEquals('/');
    }

    public function favoriteDude(AcceptanceTester $I): void
    {
        $I->amGoingTo("log in as june");
        $I->amOnPage('/login');
        $I->fillField('username', 'june');
        $I->fillField('password', '***');
        $I->click('Login');

        $I->expectTo("succeed");
        $I->seeLink('Profile', '/profile');
        $I->seeNumberOfElements('a[href^="/favorites/"]', 2);

        $I->amGoingTo("add june to my favorites");
        $I->click('a[href^="/favorites/1"]');

        $I->expectTo("see a profile picture with reaction buttons");
        $I->seeElement('a[href="/"] + form');

        $I->amGoingTo("like");
        $I->click('input[formaction$="/like"]');

        $I->expectTo("return to the main page an have my like recorded");
        $I->seeInDatabase(
            'favorites',
            [
                'user_id' => 2,
                'favorite_id' => 1,
                'rating' => 1,
            ]
        );
        $I->seeCurrentUrlEquals('/');
    }

    public function likeJuneAndDislikeDude(AcceptanceTester $I): void
    {
        $I->amGoingTo("log in as duck");
        $I->amOnPage('/login');
        $I->fillField('username', 'duck');
        $I->fillField('password', '***');
        $I->click('Login');

        $I->expectTo("succeed");
        $I->seeLink('Profile', '/profile');
        $I->seeNumberOfElements('a[href^="/favorites/"]', 2);

        $I->amGoingTo("add june to my favorites");
        $I->click('a[href^="/favorites/2"]');

        $I->expectTo("see a profile picture with reaction buttons");
        $I->seeElement('a[href="/"] + form');

        $I->amGoingTo("like");
        $I->click('input[formaction$="/like"]');

        $I->expectTo("return to the main page an have my like recorded");
        $I->seeInDatabase(
            'favorites',
            [
                'user_id' => 3,
                'favorite_id' => 2,
                'rating' => 1,
            ]
        );
        $I->seeCurrentUrlEquals('/');

        $I->amGoingTo("add down-vote dude");
        $I->click('a[href^="/favorites/1"]');

        $I->expectTo("see a profile picture with reaction buttons");
        $I->seeElement('a[href="/"] + form');

        $I->amGoingTo("like");
        $I->click('input[formaction$="/dislike"]');

        $I->expectTo("return to the main page an have my like recorded");
        $I->seeInDatabase(
            'favorites',
            [
                'user_id' => 3,
                'favorite_id' => 1,
                'rating' => -1,
            ]
        );
        $I->seeCurrentUrlEquals('/');
    }
}
