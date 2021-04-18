<?php

declare(strict_types=1);

require_once "../vendor/autoload.php";


use FastRoute\RouteCollector;
use Intervention\Image\ImageManager;
use League\Container\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Matchmaker\Config;
use Matchmaker\Controllers\HomeController;
use Matchmaker\Controllers\ImageController;
use Matchmaker\Controllers\LoginController;
use Matchmaker\Controllers\UploadController;
use Matchmaker\Repositories\ImageRepository;
use Matchmaker\Repositories\MySQLImageRepository;
use Matchmaker\Repositories\MySQLUserRepository;
use Matchmaker\Repositories\UserRepository;
use Matchmaker\Services\ImageService;
use Matchmaker\Services\LoginService;
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

$container->add(FilesystemAdapter::class, LocalFilesystemAdapter::class)
    ->addArgument(__DIR__ . '/../storage/uploads');
$container->add(Filesystem::class)
    ->addArgument(FilesystemAdapter::class);

$container->add(ImageManager::class)
    ->addArgument(
        [
            'driver' => 'gd',
        ]
    );

$container->add(LoginService::class)
    ->addArgument(UserRepository::class);
$container->add(ImageService::class)
    ->addArgument(Filesystem::class)
    ->addArgument(ImageManager::class)
    ->addArgument(UserRepository::class)
    ->addArgument(ImageRepository::class);

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
    ->addArgument(ImageService::class);
$container->add(LoginController::class)
    ->addArgument(View::class)
    ->addArgument(LoginService::class);
$container->add(UploadController::class)
    ->addArgument(View::class)
    ->addArgument(ImageService::class)
    ->addArgument(FinfoMimeTypeDetector::class);
$container->add(ImageController::class)
    ->addArgument(View::class)
    ->addArgument(ImageService::class);


$dispatcher = FastRoute\simpleDispatcher(
    function (RouteCollector $r) {
        $r->addRoute('GET', '/', [HomeController::class, 'index']);

        $r->addRoute('GET', '/login', [LoginController::class, 'login']);
        $r->addRoute('POST', '/login', [LoginController::class, 'authenticate']);
        $r->addRoute('POST', '/register', [LoginController::class, 'register']);

        $r->addRoute('GET', '/logout', [LoginController::class, 'logout']);

        $r->addRoute('POST', '/upload', [UploadController::class, 'upload']);

        $r->addRoute(['GET', 'POST'], '/images', [ImageController::class, 'images']);

        $r->addRoute('GET', '/images/{id:\d+}', [ImageController::class, 'image']);

        $r->addRoute('POST', '/images/delete/{id:\d+}', [ImageController::class, 'deleteImage']);
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
