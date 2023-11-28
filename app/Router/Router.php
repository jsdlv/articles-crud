<?php

namespace App\Router;

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

class Router
{
    public static function dispatch(): array
    {
        $dispatcher = simpleDispatcher(function (RouteCollector $r) {
            $routes = include 'routes.php';

            foreach ($routes as $route) {
                $r->addRoute($route[0], $route[1], $route[2]);
            }
        });

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }

        $uri = rawurldecode($uri);

        return $dispatcher->dispatch($httpMethod, $uri);
    }
}