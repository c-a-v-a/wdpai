<?php

require_once 'Controller.php';

class AdminPanelController extends Controller {
  private static ?AdminPanelController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new AdminPanelController();
    }

    return self::$instance;
  }

  public function index() {
    return $this->render("admin");
  }
}
