<?php

require_once 'Controller.php';

class CalendarController extends Controller {
  private static ?CalendarController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new CalendarController();
    }

    return self::$instance;
  }

  #[AllowedMethods(['GET'])]
  public function index() {
    return $this->render("calendar");
  }
}
