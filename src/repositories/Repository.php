<?php

require_once __DIR__."/../Database.php";

class Repository {
  protected static ?self $instance = null;
  protected $database;

  private function __construct() {
    $this->database = Database::getInstance();
  }

  public static function getInstance(): static {
    if (static::$instance === null) {
      static::$instance = new static();
    }

    return static::$instance;
  }
}