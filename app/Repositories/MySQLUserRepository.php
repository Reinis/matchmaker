<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use InvalidArgumentException;
use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\User;
use PDO;


class MySQLUserRepository extends MySQLRepository implements UserRepository
{
    public function create(User $user): void
    {
        $sql = "insert into `users` (username, secret, first_name, last_name, gender, profile_pic) values (?, ?, ?, ?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $user->getUsername(),
                $user->getSecret(),
                $user->getFirstName(),
                $user->getLastName(),
                $user->getGender(),
                $user->getProfilePic(),
            ]
        );
    }

    public function getAll(): Users
    {
        $sql = "select * from `users`;";
        $errorMessage = "No transactions found";

        return $this->fetchAll($sql, $errorMessage);
    }

    protected function fetchAll(string $sql, string $errorMessage, string ...$args): Users
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
        $results = $statement->fetchAll();

        if ($results === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        $users = new Users();

        foreach ($results as $result) {
            $users->add(
                new User(
                    $result->username,
                    $result->secret,
                    $result->first_name,
                    $result->last_name,
                    $result->gender,
                    $result->profile_pic,
                    $result->id,
                )
            );
        }

        return $users;
    }

    public function update(User $user): void
    {
        $sql = "update `users` set first_name = ?, last_name = ?, gender = ?, profile_pic = ? where id = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $user->getFirstName(),
                $user->getLastName(),
                $user->getGender(),
                $user->getProfilePic(),
                $user->getId(),
            ]
        );
    }

    public function delete(int $id): void
    {
        $sql = "delete from `users` where id = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
    }

    public function getUserByUsername(string $username): User
    {
        $sql = "select * from `users` where username = ?;";
        $errorMessage = "User '$username' not found";

        return $this->fetch($sql, $errorMessage, $username);
    }

    protected function fetch(string $sql, string $errorMessage, string ...$args): User
    {
        $statement = $this->connection->prepare($sql);
        $statement->setFetchMode(PDO::FETCH_OBJ);
        $statement->execute($args);
        $result = $statement->fetch();

        if ($result === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        return new User(
            $result->username,
            $result->secret,
            $result->first_name,
            $result->last_name,
            $result->gender,
            $result->profile_pic,
            $result->id,
        );
    }
}
