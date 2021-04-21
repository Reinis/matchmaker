<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use InvalidArgumentException;
use Matchmaker\Entities\Favorite;


class MySQLFavoriteRepository extends MySQLRepository implements FavoriteRepository
{
    public function create(Favorite $favorite): void
    {
        $sql = "insert into `favorites` (user_id, favorite_id, rating) values (?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $favorite->getUserId(),
                $favorite->getFavoriteId(),
                $favorite->getRating(),
            ]
        );
    }

    public function get(int $userId, int $favoriteId): Favorite
    {
        $sql = "select * from `favorites` where user_id = ? and favorite_id = ?;";
        $errorMessage = "Failed to get the favorite";

        return $this->fetch($sql, $errorMessage, (string)$userId, (string)$favoriteId);
    }

    protected function fetch(string $sql, string $errorMessage, string ...$args): Favorite
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
        $result = $statement->fetch();

        if ($result === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        return new Favorite(
            $result->user_id,
            $result->favorite_id,
            $result->rating,
            $result->id,
        );
    }

    public function update(Favorite $favorite): void
    {
        $sql = "update `favorites` set rating = ? where user_id = ? and favorite_id = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $favorite->getRating(),
                $favorite->getUserId(),
                $favorite->getFavoriteId(),
            ]
        );
    }

    public function delete(int $id): void
    {
        $sql = "delete from `favorites` where id = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
    }
}
