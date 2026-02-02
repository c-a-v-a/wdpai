<?php

require_once 'Controller.php';

class AdminPanelController extends Controller {
  #[AllowedMethods(['GET'])]
  public function index() {
    return $this->render("admin");
  }
}
