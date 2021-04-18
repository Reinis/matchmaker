<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\User;


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
}
