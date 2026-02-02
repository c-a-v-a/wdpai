<?php

require_once 'Controller.php';

class CalendarController extends Controller {
  #[AllowedMethods(['GET'])]
  public function index() {
    return $this->render("calendar");
  }
}
