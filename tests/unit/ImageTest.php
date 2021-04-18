<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use Matchmaker\Entities\Image;
use UnitTester;


class ImageTest extends Unit
{
    protected UnitTester $tester;

    public function testNewImage(): void
    {
        $image = new Image(
            'Original Image.png',
            'encoded',
            'encoded_sizexsize',
            1
        );

        self::assertEquals(null, $image->getId());
        self::assertEquals('Original Image.png', $image->getOriginalName());
        self::assertEquals('encoded', $image->getOriginalFileName());
        self::assertEquals('encoded_sizexsize', $image->getResizedFileName());
        self::assertEquals(1, $image->getUserId());
    }
}
