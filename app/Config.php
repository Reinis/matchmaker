<?php

declare(strict_types=1);


namespace Matchmaker;


use Dotenv\Dotenv;


class Config
{
    private const DB_DSN = 'MATCHMAKER_DB_DSN';
    private const DB_USER = 'MATCHMAKER_DB_USER';
    private const DB_PASSWORD = 'MATCHMAKER_DB_PASSWORD';
    private const STORAGE = 'MATCHMAKER_STORAGE';

    private string $dsn;
    private string $user;
    private string $pass;
    private string $storage;

    public function __construct(string $filename = '.env')
    {
        $this->loadDBConfig($filename);
    }

    private function loadDBConfig(string $filename): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../', $filename);
        $dotenv->load();
        $dotenv->required([self::DB_DSN, self::DB_USER, self::DB_PASSWORD, self::STORAGE]);

        $this->dsn = $_ENV[self::DB_DSN];
        $this->user = $_ENV[self::DB_USER];
        $this->pass = $_ENV[self::DB_PASSWORD];
        $this->storage = $_ENV[self::STORAGE];
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function getDBUsername(): string
    {
        return $this->user;
    }

    public function getDBPassword(): string
    {
        return $this->pass;
    }

    public function getStorageLocation(): string
    {
        return $this->storage;
    }
}
