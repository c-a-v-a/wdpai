<?php

require_once 'controllers/DashboardController.php';
require_once 'controllers/SecurityController.php';

class Router {
  public static $routes = [
    "dashboard" => [
      "action" => "index",
      "controller" => "DashboardController",
    ],
    "login" => [
      "action" => "login",
      "controller" => "SecurityController",
    ],
  ];

  public static function run(string $path, string $query) {
    if (array_key_exists($path, Router::$routes)) {
      $controller = Router::$routes[$path]["controller"];
      $action = Router::$routes[$path]["action"];

      $controllerObj = $controller::getInstance();
      $controllerObj->$action();
    } else {
      include "public/views/404.html";
    }
  }

  public static function parse(string $path) {
    preg_match_all('/([^&=]+)=([^&]*)/', $path, $matches);

    $params = array_combine($matches[1], $matches[2]);

    return $params;
  }
}
