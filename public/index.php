<?php

declare(strict_types=1);

require_once "../vendor/autoload.php";


use FastRoute\RouteCollector;
use League\Container\Container;
use Matchmaker\Config;
use Matchmaker\Controllers\HomeController;
use Matchmaker\Controllers\LoginController;
use Matchmaker\Controllers\UploadController;
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

$container->add(FilesystemLoader::class)
    ->addArgument(__DIR__ . '/../app/Views/twig');
$container->add(Environment::class)
    ->addArgument(FilesystemLoader::class)
    ->addArgument(
        [
            'cache' => __DIR__ . '/../twig_cache',
            'auto_reload' => true,
        ]
    );
$container->add(View::class, TwigView::class)
    ->addArgument(Environment::class);

$container->add(HomeController::class)
    ->addArgument(View::class);
$container->add(LoginController::class)
    ->addArgument(View::class);
$container->add(UploadController::class)
    ->addArgument(View::class);


$dispatcher = FastRoute\simpleDispatcher(
    function (RouteCollector $r) {
        $r->addRoute('GET', '/', [HomeController::class, 'index']);

        $r->addRoute('GET', '/login', [LoginController::class, 'login']);
        $r->addRoute('POST', '/login', [LoginController::class, 'authenticate']);
        $r->addRoute('POST', '/register', [LoginController::class, 'register']);

        $r->addRoute('POST', '/upload', [UploadController::class, 'upload']);
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
