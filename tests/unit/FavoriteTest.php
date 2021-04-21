<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use Matchmaker\Entities\Favorite;
use UnitTester;


class FavoriteTest extends Unit
{
    protected UnitTester $tester;

    public function testNewFavorite(): Favorite
    {
        $favorite = new Favorite(1, 3, -1);

        self::assertNull($favorite->getId());
        self::assertEquals(1, $favorite->getUserId());
        self::assertEquals(3, $favorite->getFavoriteId());
        self::assertEquals(-1, $favorite->getRating());

        return $favorite;
    }

    public function testSetRating(): void
    {
        $favorite = new Favorite(1, 3, -1);

        self::assertEquals(-1, $favorite->getRating());

        $favorite->setRating(3);

        self::assertEquals(3, $favorite->getRating());
    }

    public function testAddRating(): void
    {
        $favorite = new Favorite(1, 3, -1);

        $favorite->addRating(1);
        $favorite->addRating(1);

        self::assertEquals(1, $favorite->getRating());

        $favorite->addRating(-1);

        self::assertEquals(0, $favorite->getRating());
    }
}
