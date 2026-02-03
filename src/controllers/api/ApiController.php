<?php

class ApiController {
  protected static ?self $instance = null;

  private function __construct() {
    session_set_cookie_params([
      'httponly' => true,
      'secure' => true,
      'samesite' => 'Strict'
    ]);
    session_start();
  }

  public static function getInstance(): static {
    if (static::$instance === null) {
      static::$instance = new static();
    }

    return static::$instance;
  }

  protected function getJson() {
    $rawBody = file_get_contents('php://input');
    
    if ($rawBody === false) {
      http_response_code(400);
      throw new RuntimeException('Failed to read request body');
    }

    $data = json_decode($rawBody, true);
    if ($data === null) {
      http_response_code(400);
      throw new RuntimeException('Invalid JSON format');
    }

    return $data;
  }
}