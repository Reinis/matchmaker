<?php

declare(strict_types=1);

require_once "../vendor/autoload.php";
require_once "../app/helpers.php";


use FastRoute\RouteCollector;
use Intervention\Image\ImageManager;
use League\Container\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Matchmaker\Config;
use Matchmaker\Controllers\FavoritesController;
use Matchmaker\Controllers\HomeController;
use Matchmaker\Controllers\ImageController;
use Matchmaker\Controllers\LoginController;
use Matchmaker\Controllers\ProfileController;
use Matchmaker\Controllers\UploadController;
use Matchmaker\Repositories\FavoriteRepository;
use Matchmaker\Repositories\FilesystemRepository;
use Matchmaker\Repositories\ImageRepository;
use Matchmaker\Repositories\MySQLFavoriteRepository;
use Matchmaker\Repositories\MySQLImageRepository;
use Matchmaker\Repositories\MySQLUserRepository;
use Matchmaker\Repositories\UserRepository;
use Matchmaker\Services\FavoriteService;
use Matchmaker\Services\ImageService;
use Matchmaker\Services\LoginService;
use Matchmaker\Services\ProfileService;
use Matchmaker\Services\UserService;
use Matchmaker\StorageMap;
use Matchmaker\Views\TwigView;
use Matchmaker\Views\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


session_start();


$container = new Container();

$container->add(Config::class)
    ->addArgument('.env');
$container->add(UserRepository::class, MySQLUserRepository::class)
    ->addArgument(Config::class);
$container->add(ImageRepository::class, MySQLImageRepository::class)
    ->addArgument(Config::class);
$container->add(FavoriteRepository::class, MySQLFavoriteRepository::class)
    ->addArgument(Config::class);

try {
    $container->add(FilesystemAdapter::class, LocalFilesystemAdapter::class)
        ->addArgument(StorageMap::getPath($container->get(Config::class)->getStorageLocation()));
} catch (TypeError $e) {
    throw new RuntimeException("Internal server error");
}
$container->add(Filesystem::class)
    ->addArgument(FilesystemAdapter::class);
$container->add(FilesystemRepository::class)
    ->addArgument(Filesystem::class);

$container->add(ImageManager::class)
    ->addArgument(
        [
            'driver' => 'gd',
        ]
    );

$container->add(LoginService::class)
    ->addArgument(UserRepository::class)
    ->addArgument(ImageService::class);
$container->add(ImageService::class)
    ->addArgument(Config::class)
    ->addArgument(ImageManager::class)
    ->addArgument(UserRepository::class)
    ->addArgument(ImageRepository::class)
    ->addArgument(FilesystemRepository::class);
$container->add(ProfileService::class)
    ->addArgument(UserRepository::class)
    ->addArgument(ImageService::class);
$container->add(UserService::class)
    ->addArgument(UserRepository::class);
$container->add(FavoriteService::class)
    ->addArgument(FavoriteRepository::class);

$container->add(FilesystemLoader::class)
    ->addArgument(__DIR__ . '/../app/Views/twig');
$container->add(Environment::class)
    ->addArgument(FilesystemLoader::class)
    ->addArgument(
        [
            'cache' => __DIR__ . '/../twig_cache',
            'auto_reload' => true,
        ]
    )
    ->addMethodCall('addGlobal', ['session', $_SESSION]);
$container->add(View::class, TwigView::class)
    ->addArgument(Environment::class);

$container->add(FinfoMimeTypeDetector::class);

$container->add(HomeController::class)
    ->addArgument(View::class)
    ->addArgument(ProfileService::class)
    ->addArgument(UserService::class);
$container->add(LoginController::class)
    ->addArgument(View::class)
    ->addArgument(LoginService::class);
$container->add(UploadController::class)
    ->addArgument(ImageService::class)
    ->addArgument(FinfoMimeTypeDetector::class);
$container->add(ImageController::class)
    ->addArgument(View::class)
    ->addArgument(ProfileService::class)
    ->addArgument(ImageService::class);
$container->add(ProfileController::class)
    ->addArgument(View::class)
    ->addArgument(ProfileService::class);
$container->add(FavoritesController::class)
    ->addArgument(View::class)
    ->addArgument(ProfileService::class)
    ->addArgument(ImageService::class)
    ->addArgument(FavoriteService::class);


$dispatcher = FastRoute\simpleDispatcher(
    function (RouteCollector $r) {
        $r->addRoute('GET', '/', [HomeController::class, 'index']);

        $r->addRoute('GET', '/login', [LoginController::class, 'login']);
        $r->addRoute('POST', '/login', [LoginController::class, 'authenticate']);
        $r->addRoute('GET', '/register', [LoginController::class, 'registration']);
        $r->addRoute('POST', '/register', [LoginController::class, 'register']);

        $r->addRoute('GET', '/profile', [ProfileController::class, 'profile']);
        $r->addRoute('POST', '/profile/image', [ProfileController::class, 'setPicture']);

        $r->addRoute('GET', '/logout', [LoginController::class, 'logout']);

        $r->addRoute('POST', '/users/delete', [LoginController::class, 'deleteAccount']);

        $r->addRoute('POST', '/upload', [UploadController::class, 'upload']);

        $r->addRoute(['GET', 'POST'], '/images', [ImageController::class, 'images']);

        $r->addRoute('GET', '/images/{id:\d+}', [ImageController::class, 'image']);

        $r->addRoute('POST', '/images/delete/{id:\d+}', [ImageController::class, 'deleteImage']);

        $r->addRoute('GET', '/favorites/{id:\d+}', [FavoritesController::class, 'rating']);

        $r->addRoute('POST', '/favorites/{id:\d+}/{rating:like|dislike}', [FavoritesController::class, 'rate']);
    }
);

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $container->get($class)->$method($vars);
        break;
}

if ('GET' === $httpMethod && isset($_SESSION['_flash'])) {
    unset($_SESSION['_flash']);
}
