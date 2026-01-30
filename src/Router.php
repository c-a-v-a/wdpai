<?php

require_once 'controllers/AdminPanelController.php';
require_once 'controllers/CalendarController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/MessageBoardController.php';
require_once 'controllers/SecurityController.php';
require_once 'controllers/api/CommentApiController.php';
require_once 'controllers/api/EventApiController.php';
require_once 'controllers/api/MessageApiController.php';
require_once 'middleware/checkRequestAllowed.php';

class Router {
  public static $routes = [
    "admin" => [
      "action" => "index",
      "controller" => "AdminPanelController",
    ],
    "calendar" => [
      "action" => "index",
      "controller" => "CalendarController",
    ],
    "dashboard" => [
      "action" => "index",
      "controller" => "DashboardController",
    ],
    "messageboard" => [
      "action" => "index",
      "controller" => "MessageBoardController",
    ],
    "login" => [
      "action" => "login",
      "controller" => "SecurityController",
    ],
    "api/comment" => [
      "action" => "addComment",
      "controller" => "CommentApiController"
    ],
    "api/event" => [
      "action" => "index",
      "controller" => "EventApiController"
    ],
    "api/message" => [
      "action" => "index",
      "controller" => "MessageApiController"
    ]
  ];

  public static function run(string $path, string $query) {
    if (array_key_exists($path, Router::$routes)) {
      $controller = Router::$routes[$path]["controller"];
      $action = Router::$routes[$path]["action"];
      $controllerObj = $controller::getInstance();

      try {
        checkRequestAllowed($controllerObj, $action);

        $controllerObj->$action(Router::parse($query));
      } catch(Exception $e) {
        echo "ERROR: " . $e->getMessage();
      }
    } else {
      include "public/views/404.html";
    }
  }

  public static function parse(string $query) {
    preg_match_all('/([^&=]+)=([^&]*)/', $query, $matches);

    $params = array_combine($matches[1], $matches[2]);

    return $params;
  }
}
