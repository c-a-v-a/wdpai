<?php

class Controller {
  protected static ?self $instance = null;

  private function __construct() {
    session_start();
  }

  public static function getInstance(): static {
    if (static::$instance === null) {
      static::$instance = new static();
    }

    return static::$instance;
  }

  protected function render(string $template = "", array $variables =[]) {
    $templatePath = 'public/views/' . $template . '.html';
    $templatePath404 = 'public/views/404.html';
    $output = "";

    if (file_exists($templatePath)) {
      extract($variables);
      ob_start();

      include $templatePath;

      $output = ob_get_clean();
    } else {
      ob_start();

      include $templatePath404;

      $output = ob_get_clean();
    }

    echo $output;
  }
}
