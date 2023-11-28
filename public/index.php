<?php

declare(strict_types=1);

session_start();

require_once '../vendor/autoload.php';

use App\Response\RedirectResponse;
use App\Response\ViewResponse;
use App\Router\Router;
use FastRoute\Dispatcher;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader);

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (isset($_SESSION['flush'])) {
    foreach ($_SESSION['flush'] as $sessionKey => $sessionValue) {
        if (is_array($sessionValue) === true) {
            foreach ($sessionValue as $key => $value) {
                $twig->addGlobal($key, $value);
                break;
            }
        }
    }
}

$routeInfo = Router::dispatch();

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        echo $twig->render('404.twig');
        break;
    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        break;
    case Dispatcher::FOUND:
        [$className, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        $response = (new $className())->{$method}(...array_values($vars));

        switch (true) {
            case $response instanceof ViewResponse:
                echo $twig->render($response->getViewName() . '.twig', $response->getData());
                unset($_SESSION['flush']);
                break;
            case $response instanceof RedirectResponse:
                header('Location: ' . $response->getLocation());
                break;
            default:
                // throw new exception
                break;
        }
        break;
}