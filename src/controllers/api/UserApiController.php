<?php

require_once 'ApiController.php';
require_once __DIR__.'/../../data/UserCreateDTO.php';
require_once __DIR__.'/../../repositories/UserRepository.php';

class UserApiController extends ApiController {
  #[PublicRoute]
  #[AllowedMethods(['POST'])]
  public function login() {
    $data = $this->getJson();

    foreach (['email', 'password'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $user = UserRepository::getInstance()->getUser($data['email']);

    if ($user === false || !password_verify($data['password'], $user->password)) {
      http_response_code(401);
      throw new RuntimeException("Email or password incorrect.");
    }

    $_SESSION['id'] = $user->id;
    $_SESSION['email'] = $user->email;

    echo json_encode(['success' => true]);
  }

  #[AllowedMethods(['POST'])]
  public function logout() {
    session_destroy();

    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
          session_name(),
          '',
          time() - 42000,
          $params['path'],
          $params['domain'],
          $params['secure'],
          $params['httponly']
      );
    }

    echo json_encode(['success' => true]);
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
    $data = $this->getJson();

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