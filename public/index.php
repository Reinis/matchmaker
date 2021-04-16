<?php

declare(strict_types=1);

require_once "../vendor/autoload.php";


use League\Container\Container;
use Matchmaker\Config;
use Matchmaker\Repositories\MySQLUserRepository;
use Matchmaker\Repositories\UserRepository;


$container = new Container();

$container->add(Config::class)
    ->addArgument('.env');
$container->add(UserRepository::class, MySQLUserRepository::class)
    ->addArgument(Config::class);
