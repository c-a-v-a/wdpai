<?php

require_once 'Controller.php';

class MessageBoardController extends Controller {
  private static ?MessageBoardController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new MessageBoardController();
    }

    return self::$instance;
  }

  public function index() {
    return $this->render("messageboard");
  }
}
