<?php

class Controller {
  protected function render(string $template = "", array $variables =[]) {
    $templatePath = 'public/views/' . $template . '.php.html';
    $templatePath404 = 'public/views/dashboard.html';
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
