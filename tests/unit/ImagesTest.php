<?php

declare(strict_types=1);


namespace MatchmakerTests\Unit;


use Codeception\Test\Unit;
use DateTime;
use Matchmaker\Entities\Collections\Images;
use Matchmaker\Entities\Image;
use UnitTester;


class ImagesTest extends Unit
{
    protected UnitTester $tester;

    public function testNewUsers(): void
    {
        $images = new Images(
            new Image('orig.png', 'test', 'abc', 'abc_4x4', new DateTime('2021-01-01 12:34:45'), 1),
            new Image('orig2.png', 'test', 'def', 'def_4x4', new DateTime('2021-01-01 12:34:45'), 2),
        );

        self::assertCount(2, $images);

        foreach ($images as $image) {
            self::assertContainsEquals($image->getOriginalName(), ['orig.png', 'orig2.png']);
            self::assertContainsEquals($image->getOriginalFileName(), ['abc', 'def']);
            self::assertContainsEquals($image->getResizedFileName(), ['abc_4x4', 'def_4x4']);
            self::assertContainsEquals($image->getUploadTime(), [new DateTime('2021-01-01 12:34:45')]);
            self::assertContainsEquals($image->getUserId(), [1, 2]);
        }
    }
}
