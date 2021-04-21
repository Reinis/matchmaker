<?php

declare(strict_types=1);


namespace MatchmakerTests\Integration;


use Codeception\Test\Unit;
use IntegrationTester;
use Matchmaker\Config;
use Matchmaker\Entities\Favorite;
use Matchmaker\Repositories\MySQLFavoriteRepository;
use PDO;


class MySQLFavoriteRepositoryTest extends Unit
{
    private const CONFIG_FILENAME = '.env.test';

    protected IntegrationTester $tester;

    private ?MySQLFavoriteRepository $repository = null;
    private ?PDO $connection = null;

    public function testAdd(): void
    {
        $favorite = new Favorite(1, 2, 1);
        $this->repository->create($favorite);

        $this->tester->seeInDatabase(
            'favorites',
            [
                'user_id' => 1,
                'favorite_id' => 2,
                'rating' => 1,
            ]
        );
    }

    public function testGet(): void
    {
        $this->tester->haveInDatabase(
            'favorites',
            [
                'user_id' => 1,
                'favorite_id' => 2,
                'rating' => 3,
            ]
        );

        $favorite = $this->repository->get(1, 2);

        self::assertNotNull($favorite->getId());
        self::assertEquals(1, $favorite->getUserId());
        self::assertEquals(2, $favorite->getFavoriteId());
        self::assertEquals(3, $favorite->getRating());
    }

    public function testUpdate(): void
    {
        $this->tester->haveInDatabase(
            'favorites',
            [
                'user_id' => 1,
                'favorite_id' => 2,
                'rating' => 1,
            ]
        );

        $favorite = $this->repository->get(1, 2);

        self::assertEquals(1, $favorite->getRating());

        $favorite->setRating(-2);

        $this->repository->update($favorite);

        $this->tester->seeInDatabase(
            'favorites',
            [
                'user_id' => 1,
                'favorite_id' => 2,
                'rating' => -2,
            ]
        );
    }

    public function testDelete(): void
    {
        $this->tester->haveInDatabase(
            'favorites',
            [
                'user_id' => 1,
                'favorite_id' => 2,
                'rating' => 1,
            ]
        );

        $this->tester->seeNumRecords(1, 'favorites');

        $favorite = $this->repository->get(1, 2);
        $this->repository->delete($favorite->getId());

        $this->tester->seeNumRecords(0, 'favorites');
    }

    protected function _before(): void
    {
        if (null === $this->connection || null === $this->repository) {
            $config = new Config(self::CONFIG_FILENAME);

            $this->repository = new MySQLFavoriteRepository($config);
            $this->connection = $this->getModule('Db')->dbhs['default'];
        }

        $this->resetTestDBTables();
    }

    private function resetTestDBTables(): void
    {
        $sql = "drop table if exists `matchmaker_test`.`favorites`;";
        $this->connection->exec($sql);

        $sql = "create table `matchmaker_test`.`favorites` like `matchmaker`.`favorites`;";
        $this->connection->exec($sql);
    }
}
