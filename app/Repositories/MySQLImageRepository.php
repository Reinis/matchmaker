<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use DateTime;
use InvalidArgumentException;
use Matchmaker\Entities\Collections\Images;
use Matchmaker\Entities\Image;


class MySQLImageRepository extends MySQLRepository implements ImageRepository
{
    public function create(Image $image): void
    {
        $sql = "insert into `pictures` (original_name, storage, original_file, resized_file, upload_time, user_id) values (?, ?, ?, ?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $image->getOriginalName(),
                $image->getStorageLocation(),
                $image->getOriginalFileName(),
                $image->getResizedFileName(),
                $image->getUploadTime()->format('Y-m-d H:i:s'),
                $image->getUserId(),
            ]
        );
    }

    public function getAllUserImages(string $username): Images
    {
        $sql = "select * from `pictures` where user_id = (select id from `users` where username = ?);";
        $errorMessage = "No images found";

        return $this->fetchAll($sql, $errorMessage, $username);
    }

    protected function fetchAll(string $sql, string $errorMessage, string ...$args): Images
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
        $results = $statement->fetchAll();

        if ($results === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        $users = new Images();

        foreach ($results as $result) {
            $users->add(
                new Image(
                    $result->original_name,
                    $result->storage,
                    $result->original_file,
                    $result->resized_file,
                    new DateTime($result->upload_time),
                    $result->user_id,
                    $result->id,
                )
            );
        }

        return $users;
    }

    public function getById(int $id): Image
    {
        $sql = "select * from `pictures` where id = ?;";
        $errorMessage = "Image not found";

        return $this->fetch($sql, $errorMessage, (string)$id);
    }

    protected function fetch(string $sql, string $errorMessage, string ...$args): Image
    {
        $statement = $this->connection->prepare($sql);
        $statement->execute($args);
        $result = $statement->fetch();

        if ($result === false) {
            throw new InvalidArgumentException($errorMessage);
        }

        return new Image(
            $result->original_name,
            $result->storage,
            $result->original_file,
            $result->resized_file,
            new DateTime($result->upload_time),
            $result->user_id,
            $result->id,
        );
    }

    public function delete(int $id): void
    {
        $sql = "delete from `pictures` where id = ?;";
        $statement = $this->connection->prepare($sql);
        $statement->execute([$id]);
    }
}
