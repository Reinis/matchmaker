<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use DateTime;
use InvalidArgumentException;
use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\Image;
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
                $user->getProfilePicId(),
            ]
        );
    }

    public function getAll(): Users
    {
        $sql = <<<EOE
        select
            `users`.id as user_id, username, secret, first_name, last_name, gender,
            profile_pic as img_id, original_name, storage, original_file, resized_file, upload_time
        from `users` left join `pictures` on `users`.profile_pic = `pictures`.id
        EOE;

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
            $image = null;

            if ($result->img_id !== null) {
                $image = new Image(
                    $result->original_name,
                    $result->storage,
                    $result->original_file,
                    $result->resized_file,
                    new DateTime($result->upload_time),
                    $result->user_id,
                    $result->img_id,
                );
            }

            $users->add(
                new User(
                    $result->username,
                    $result->secret,
                    $result->first_name,
                    $result->last_name,
                    $result->gender,
                    $image,
                    $result->user_id,
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
                $user->getProfilePicId(),
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
        $sql = <<<EOE
        select
               `users`.id as user_id, username, secret, first_name, last_name, gender,
               profile_pic as img_id, original_name, storage, original_file, resized_file, upload_time
        from `users` left join `pictures` on `users`.profile_pic = `pictures`.id
        where username = ?;
        EOE;
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

        $image = null;

        if ($result->img_id !== null) {
            $image = new Image(
                $result->original_name,
                $result->storage,
                $result->original_file,
                $result->resized_file,
                new DateTime($result->upload_time),
                $result->user_id,
                $result->img_id,
            );
        }

        return new User(
            $result->username,
            $result->secret,
            $result->first_name,
            $result->last_name,
            $result->gender,
            $image,
            $result->user_id,
        );
    }

    public function getOtherUsers(User $user): Users
    {
        $sql = <<<EOE
        select
            `users`.id as user_id, username, secret, first_name, last_name, gender,
            profile_pic as img_id, original_name, storage, original_file, resized_file, upload_time
        from `users` left join `pictures` on `users`.profile_pic = `pictures`.id
        where `users`.id != ? and `users`.gender != ?;
        EOE;
        $errorMessage = "Other users not found";

        return $this->fetchAll($sql, $errorMessage, (string)$user->getId(), $user->getGender());
    }

    public function get(int $id): User
    {
        $sql = <<<EOE
        select
               `users`.id as user_id, username, secret, first_name, last_name, gender,
               profile_pic as img_id, original_name, storage, original_file, resized_file, upload_time
        from `users` left join `pictures` on `users`.profile_pic = `pictures`.id
        where user_id = ?;
        EOE;
        $errorMessage = "User '$id' not found";

        return $this->fetch($sql, $errorMessage, (string)$id);
    }
}
