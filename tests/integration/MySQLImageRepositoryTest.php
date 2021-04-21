<?php

declare(strict_types=1);


namespace MatchmakerTests\Integration;


use Codeception\Test\Unit;
use DateTime;
use IntegrationTester;
use Matchmaker\Config;
use Matchmaker\Entities\Image;
use Matchmaker\Repositories\MySQLImageRepository;
use Matchmaker\StorageMap;
use PDO;


class MySQLImageRepositoryTest extends Unit
{
    private const CONFIG_FILENAME = '.env.test';

    protected IntegrationTester $tester;

    private ?MySQLImageRepository $repository = null;
    private ?PDO $connection = null;

    public function testAdd(): void
    {
        $image = new Image(
            'image.png',
            'test',
            'random',
            'randoms',
            new DateTime('2021-01-01 12:34:56'),
            1
        );
        $this->repository->create($image);

        $this->tester->seeInDatabase(
            'pictures',
            [
                'original_name' => 'image.png',
                'storage' => 'test',
                'original_file' => 'random',
                'resized_file' => 'randoms',
                'upload_time' => '2021-01-01 12:34:56',
            ]
        );
    }

    public function testGetAll(): void
    {
        $this->tester->haveInDatabase(
            'pictures',
            [
                'original_name' => 'image.png',
                'storage' => 'test',
                'original_file' => 'random',
                'resized_file' => 'randoms',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 1,
            ]
        );
        $this->tester->haveInDatabase(
            'pictures',
            [
                'original_name' => 'another image.png',
                'storage' => 'test',
                'original_file' => 'random2',
                'resized_file' => 'randoms2',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 1,
            ]
        );

        $images = $this->repository->getAllUserImages('go');

        self::assertCount(2, $images);

        foreach ($images as $image) {
            self::assertContainsEquals($image->getOriginalName(), ['image.png', 'another image.png']);
            self::assertContainsEquals($image->getStorageLocation(), ['test']);
            self::assertContainsEquals($image->getOriginalFileName(), ['random', 'random2']);
            self::assertContainsEquals($image->getResizedFileName(), ['randoms', 'randoms2']);
            self::assertContainsEquals($image->getUploadTime(), [new DateTime('2021-01-01 12:34:56')]);
            self::assertContainsEquals($image->getUserId(), [1]);
        }
    }

    public function testGetById(): void
    {
        $this->tester->haveInDatabase(
            'pictures',
            [
                'id' => 3,
                'original_name' => 'image.png',
                'storage' => 'test',
                'original_file' => 'random',
                'resized_file' => 'randoms',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 1,
            ]
        );

        $image = $this->repository->getById(3);

        self::assertEquals(3, $image->getId());
        self::assertEquals('image.png', $image->getOriginalName());
        self::assertEquals('test', $image->getStorageLocation());
        self::assertEquals('random', $image->getOriginalFileName());
        self::assertEquals('ra/nd/random', $image->getOriginalFilePath());
        self::assertEquals('randoms', $image->getResizedFileName());
        self::assertEquals('ra/nd/randoms', $image->getResizedFilePath());
        self::assertEquals(StorageMap::getImageDir('test') . '/ra/nd/randoms', $image->getResizedImageExtendedPath());
        self::assertEquals('2021-01-01 12:34:56', $image->getUploadTime()->format('Y-m-d H:i:s'));
        self::assertEquals(1, $image->getUserId());
    }

    public function testDelete(): void
    {
        $this->tester->haveInDatabase(
            'pictures',
            [
                'id' => 3,
                'original_name' => 'image.png',
                'storage' => 'test',
                'original_file' => 'random',
                'resized_file' => 'randoms',
                'upload_time' => '2021-01-01 12:34:56',
                'user_id' => 1,
            ]
        );

        $this->tester->seeNumRecords(1, 'pictures');

        $image = $this->repository->getAllUserImages('go')->getIterator()->current();
        $this->repository->delete($image->getId());

        $this->tester->seeNumRecords(0, 'pictures');
    }

    public function _before(): void
    {
        if (null === $this->connection || null === $this->repository) {
            $config = new Config(self::CONFIG_FILENAME);

            $this->repository = new MySQLImageRepository($config);
            $this->connection = $this->getModule('Db')->dbhs['default'];
        }

        $this->resetTestDBTables();
        $this->tester->haveInDatabase(
            'users',
            [
                'id' => 1,
                'username' => 'go',
                'secret' => '***',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male'
            ]
        );
    }

    private function resetTestDBTables(): void
    {
        $sql = "drop table if exists `matchmaker_test`.`pictures`;";
        $this->connection->exec($sql);

        $sql = "create table `matchmaker_test`.`pictures` like `matchmaker`.`pictures`;";
        $this->connection->exec($sql);
    }
}