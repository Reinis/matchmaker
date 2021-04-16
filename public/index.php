<?php

declare(strict_types=1);

require_once "../vendor/autoload.php";


use League\Container\Container;
use Matchmaker\Config;
use Matchmaker\Repositories\MySQLUserRepository;
use Matchmaker\Repositories\UserRepository;
use Matchmaker\Views\TwigView;
use Matchmaker\Views\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$container = new Container();

$container->add(Config::class)
    ->addArgument('.env');
$container->add(UserRepository::class, MySQLUserRepository::class)
    ->addArgument(Config::class);

$container->add(FilesystemLoader::class, FilesystemLoader::class)
    ->addArgument(__DIR__ . '/../app/Views/twig');
$container->add(Environment::class, Environment::class)
    ->addArgument(FilesystemLoader::class)
    ->addArgument(
        [
            'cache' => __DIR__ . '/../twig_cache',
            'auto_reload' => true,
        ]
    );
$container->add(View::class, TwigView::class)
    ->addArgument(Environment::class);
