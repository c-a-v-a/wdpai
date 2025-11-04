<?php

require_once 'Controller.php';

class SecurityController extends Controller {
  private static ?SecurityController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new SecurityController();
    }

    return self::$instance;
  }

  public function login() {
    return $this->render("login");
  }

  public function register() {
    return $this->render("register");
  }
}
