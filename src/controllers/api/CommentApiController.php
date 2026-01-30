<?php

require_once __DIR__.'/../../data/CommentCreateDTO.php';
require_once __DIR__.'/../../repositories/CommentRepository.php';

class CommentApiController {
  private static ?CommentApiController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new CommentApiController();
    }

    return self::$instance;
  }

  #[AllowedMethods(['POST'])]
  public function addComment() {
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

    foreach (['user_id', 'message_id', 'content'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $dto = new CommentCreateDTO();
    $dto->user_id = (int)$data['user_id'];
    $dto->message_id = (int)$data['message_id'];
    $dto->content = (string)$data['content'];
    $id = CommentRepository::getInstance()->addComment($dto);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $id]);
  }
}