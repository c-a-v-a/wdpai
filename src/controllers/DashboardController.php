<?php

require_once 'Controller.php';

class DashboardController extends Controller {
  #[AllowedMethods(['GET'])]
  public function index() {
    return $this->render("dashboard");
  }
}
