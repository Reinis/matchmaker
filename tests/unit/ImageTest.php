<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use DateTime;
use Matchmaker\Entities\Image;
use UnitTester;


class ImageTest extends Unit
{
    protected UnitTester $tester;

    public function testNewImage(): Image
    {
        $image = new Image(
            'Original Image.png',
            'encoded',
            'encoded_sizexsize',
            new DateTime('2021-01-01 12:34:56'),
            1
        );

        self::assertEquals(null, $image->getId());
        self::assertEquals('Original Image.png', $image->getOriginalName());
        self::assertEquals('encoded', $image->getOriginalFileName());
        self::assertEquals('encoded_sizexsize', $image->getResizedFileName());
        self::assertEquals(new DateTime('2021-01-01 12:34:56'), $image->getUploadTime());
        self::assertEquals(1, $image->getUserId());

        return $image;
    }

    /**
     * @depends testNewImage
     */
    public function testGetResizedFileLocation(Image $image): void
    {
        self::assertEquals('en/co/encoded_sizexsize', $image->getResizedFileLocation());
    }

    /**
     * @depends testNewImage
     */
    public function testGetOriginalFileLocation(Image $image): void
    {
        self::assertEquals('en/co/encoded', $image->getOriginalFileLocation());
    }
}
