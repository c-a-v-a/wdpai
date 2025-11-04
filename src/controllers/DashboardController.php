<?php

require_once 'Controller.php';

class DashboardController extends Controller {
  private static ?DashboardController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new DashboardController();
    }

    return self::$instance;
  }

  public function index() {
    return $this->render("dashboard");
  }
}
