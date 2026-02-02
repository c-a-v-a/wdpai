<?php

require_once 'Controller.php';

class SecurityController extends Controller {
  #[PublicRoute]
  #[AllowedMethods(['GET'])]
  public function login() {
    return $this->render("login");
  }
}
