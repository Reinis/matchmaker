<?php

declare(strict_types=1);


namespace MatchmakerTests\Integration;


use Codeception\Test\Unit;
use IntegrationTester;
use Matchmaker\Config;
use Matchmaker\Entities\User;
use Matchmaker\Repositories\MySQLUserRepository;
use PDO;


class MySQLUserRepositoryTest extends Unit
{
    private const CONFIG_FILENAME = '.env.test';

    protected IntegrationTester $tester;

    private ?MySQLUserRepository $repository = null;
    private ?PDO $connection = null;

    public function testAddTransaction(): void
    {
        $user = new User('John', 'Doe', 'male');
        $this->repository->create($user);

        $this->tester->seeInDatabase(
            'users',
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male'
            ]
        );
    }

    public function testGetAll(): void
    {
        $this->tester->haveInDatabase(
            'users',
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male'
            ]
        );
        $this->tester->haveInDatabase(
            'users',
            [
                'first_name' => 'Jane',
                'last_name' => 'Snu',
                'gender' => 'female'
            ]
        );

        $users = $this->repository->getAll();

        self::assertCount(2, $users);

        foreach ($users as $user) {
            self::assertContainsEquals($user->getFirstName(), ['John', 'Jane']);
            self::assertContainsEquals($user->getLastName(), ['Doe', 'Snu']);
            self::assertContainsEquals($user->getGender(), ['male', 'female']);
            self::assertContainsEquals($user->getProfilePic(), ['Default']);
        }
    }

    public function testUpdate(): void
    {
        $this->tester->haveInDatabase(
            'users',
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
            ]
        );

        $user = $this->repository->getAll()->getIterator()->current();

        $user->setFirstName('Jane');
        $user->setLastName('Snu');
        $user->setGender('female');

        $this->repository->update($user);

        $this->tester->seeInDatabase(
            'users',
            [
                'first_name' => 'Jane',
                'last_name' => 'Snu',
                'gender' => 'female',
            ]
        );
    }

    public function testDelete(): void
    {
        $this->tester->haveInDatabase(
            'users',
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
            ]
        );

        $this->tester->seeNumRecords(1, 'users');

        $user = $this->repository->getAll()->getIterator()->current();
        $this->repository->delete($user->getId());

        $this->tester->seeNumRecords(0, 'users');
    }

    public function _before(): void
    {
        if (null === $this->connection || null === $this->repository) {
            $config = new Config(self::CONFIG_FILENAME);

            $this->repository = new MySQLUserRepository($config);
            $this->connection = $this->getModule('Db')->dbhs['default'];
        }

        $this->resetTestDBTables();
    }

    private function resetTestDBTables(): void
    {
        $sql = "drop table if exists `matchmaker_test`.`users`;";
        $this->connection->exec($sql);

        $sql = "create table `matchmaker_test`.`users` like `matchmaker`.`users`;";
        $this->connection->exec($sql);
    }
}
