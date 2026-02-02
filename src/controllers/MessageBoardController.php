<?php

require_once 'Controller.php';

class MessageBoardController extends Controller {
  #[AllowedMethods(['GET'])]
  public function index() {
    return $this->render("messageboard");
  }
}
