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

    public function testAdd(): void
    {
        $user = new User('Johnny', '***', 'John', 'Doe', 'male');
        $this->repository->create($user);

        $this->tester->seeInDatabase(
            'users',
            [
                'username' => 'Johnny',
                'secret' => '***',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male',
                'profile_pic' => null,
            ]
        );

        // Clean up the database for a single test function
        $sql = "delete from `users` where username = 'Johnny'; alter table `users` auto_increment=1;";
        $this->connection->exec($sql);
    }

    public function testGetAll(): void
    {
        $this->tester->haveInDatabase(
            'users',
            [
                'username' => 'Jonny',
                'secret' => '***',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'gender' => 'male'
            ]
        );
        $this->tester->haveInDatabase(
            'users',
            [
                'username' => 'Jane',
                'secret' => '****',
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
            self::assertContainsEquals($user->getProfilePic(), [null]);
        }
    }

    public function testUpdate(): void
    {
        $this->tester->haveInDatabase(
            'users',
            [
                'username' => 'Jonny',
                'secret' => '***',
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
                'username' => 'Jonny',
                'secret' => '***',
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

    protected function _before(): void
    {
        if (null === $this->connection || null === $this->repository) {
            $config = new Config(self::CONFIG_FILENAME);

            $this->repository = new MySQLUserRepository($config);
            $this->connection = $this->getModule('Db')->dbhs['default'];
        }
    }
}
