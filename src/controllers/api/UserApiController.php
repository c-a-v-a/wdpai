<?php

require_once __DIR__.'/../../data/UserCreateDTO.php';
require_once __DIR__.'/../../repositories/UserRepository.php';

class UserApiController {
  private static ?UserApiController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new UserApiController();
    }

    return self::$instance;
  }

  #[AllowedMethods(['GET', 'POST'])]
  public function index() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->addUser();
    } else {
      $this->getUsers();
    }
  }

  #[AllowedMethods(['PUT'])]
  public function enableUser(array $params) {
    if (!array_key_exists('id', $params)) {
      http_response_code(400);
      throw new RuntimeException('Invalid query params');
    }
    
    $userId = $params['id'];

    echo json_encode(['success' => UserRepository::getInstance()->enableUser($userId)]);
  }

  #[AllowedMethods(['PUT'])]
  public function disableUser(array $params) {
    if (!array_key_exists('id', $params)) {
      http_response_code(400);
      throw new RuntimeException('Invalid query params');
    }

    $userId = $params['id'];

    echo json_encode(['success' => UserRepository::getInstance()->disableUser($userId)]);
  }

  private function addUser() {
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

    foreach (['first_name', 'surname', 'email', 'password'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $dto = new UserCreateDTO();
    $dto->first_name = (string)$data['first_name'];
    $dto->surname = (string)$data['surname'];
    $dto->email = (string)$data['email'];
    $dto->password = (string)$data['password'];
    $id = UserRepository::getInstance()->addUser($dto);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $id]);

    return;
  }

  private function getUsers() {
    $users = UserRepository::getInstance()->getUsers();

    header('Content-Type: application/json');
    echo json_encode($users);
    
    return;
  }
}