<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use DateTime;


class MySQLImageRepository extends MySQLRepository implements ImageRepository
{
    public function save(string $originalName, string $originalFile, string $resizedFile, int $userId): void
    {
        $sql = "insert into pictures (original_name, original_file, resized_file, time, user_id) values (?, ?, ?, ?, ?);";
        $statement = $this->connection->prepare($sql);
        $statement->execute(
            [
                $originalName,
                $originalFile,
                $resizedFile,
                (new DateTime('now'))->format('Y-m-d H:i:s'),
                $userId,
            ]
        );
    }
}
