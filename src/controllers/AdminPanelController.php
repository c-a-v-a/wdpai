<?php

require_once 'Controller.php';

class AdminPanelController extends Controller {
  #[AdminOnly]
  #[AllowedMethods(['GET'])]
  public function index() {
    return $this->render("admin");
  }
}
