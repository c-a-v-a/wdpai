<?php

class Router {
  public static function run(string $path) {
    switch ($path) {
      case '':
        include 'public/views/index.html';
        break;
      case 'login':
        include 'public/views/login.html';
        break;
      default:
        include 'public/views/404.html';
        break;
    }
  }
}
