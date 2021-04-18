<?php

declare(strict_types=1);


namespace Matchmaker\Repositories;


use InvalidArgumentException;
use Matchmaker\Config;
use Matchmaker\Entities\Collections\Users;
use Matchmaker\Entities\User;
use PDO;
use PDOException;


class MySQLRepository
{
    protected PDO $connection;

    public function __construct(Config $config)
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->connection = new PDO(
                $config->getDsn(),
                $config->getDBUsername(),
                $config->getDBPassword(),
                $options
            );
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
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
}
