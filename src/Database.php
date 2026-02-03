<?php

class Database {
  private static ?Database $instance = null;

  private ?PDO $connection = null;

  private string $username;
  private string $password;
  private string $host;
  private string $database;
  private int $port;

  private function __construct() {
    $this->username = getenv('DB_USER');
    $this->password = getenv('DB_PASSWORD');
    $this->host = "db";
    $this->database = getenv('DB_NAME');
    $this->port = 5432;
  }

  public static function getInstance(): Database {
    if (self::$instance === null) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  private function connect() {
    try {
      $conn = new PDO(
        "pgsql:host=$this->host;port=$this->port;dbname=$this->database",
        $this->username,
        $this->password,
        ["sslmode" => "prefer"]
      );

      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      return $conn;
    } catch (PDOException $e) {
      throw new Exception("Server error");
    }
  }

  public function getConnection(): PDO {
    if ($this->connection === null) {
      $this->connection = $this->connect();
    }

    return $this->connection;
  }
}