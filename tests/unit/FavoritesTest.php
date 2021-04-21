<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use Matchmaker\Entities\Collections\Favorites;
use Matchmaker\Entities\Favorite;
use UnitTester;


class FavoritesTest extends Unit
{
    protected UnitTester $tester;

    public function testNewFavorites(): void
    {
        $favorites = new Favorites(
            new Favorite(1, 3, -1),
            new Favorite(2, 3, 2),
            new Favorite(3, 1, 0),
        );

        self::assertCount(3, $favorites);

        foreach ($favorites as $favorite) {
            self::assertNull($favorite->getId());
            self::assertContainsEquals($favorite->getUserId(), [1,2,3]);
            self::assertContainsEquals($favorite->getFavoriteId(), [1,3]);
            self::assertContainsEquals($favorite->getRating(), [-1,0,2]);
        }
    }
}
