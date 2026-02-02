<?php

require_once 'controllers/AdminPanelController.php';
require_once 'controllers/CalendarController.php';
require_once 'controllers/DashboardController.php';
require_once 'controllers/MessageBoardController.php';
require_once 'controllers/SecurityController.php';
require_once 'controllers/api/CommentApiController.php';
require_once 'controllers/api/EventApiController.php';
require_once 'controllers/api/MessageApiController.php';
require_once 'controllers/api/UserApiController.php';
require_once 'middleware/checkRequestAllowed.php';
require_once 'middleware/checkIsAuthorized.php';

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
    ],
    "api/user" => [
      "action" => "index",
      "controller" => "UserApiController"
    ],
    "api/user/enable" => [
      "action" => "enableUser",
      "controller" => "UserApiController"
    ],
    "api/user/disable" => [
      "action" => "disableUser",
      "controller" => "UserApiController"
    ],
    "api/login" => [
      "action" => "login",
      "controller" => "UserApiController"
    ],
    "api/logout" => [
      "action" => "logout",
      "controller" => "UserApiController"
    ]
  ];

  public static function run(string $path, string $query) {
    if (array_key_exists($path, Router::$routes)) {
      $controller = Router::$routes[$path]["controller"];
      $action = Router::$routes[$path]["action"];
      $controllerObj = $controller::getInstance();

      
      if (!checkRequestAllowed($controllerObj, $action)) {
        http_response_code(405);
      } else if (!checkIsAuthorized($controllerObj, $action)) {
        http_response_code(401);
      } else {
        $controllerObj->$action(Router::parse($query));
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
