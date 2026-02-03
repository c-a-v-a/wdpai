<?php

require_once 'ApiController.php';
require_once __DIR__.'/../../data/CommentCreateDTO.php';
require_once __DIR__.'/../../repositories/CommentRepository.php';

class CommentApiController extends ApiController {
  #[AllowedMethods(['POST'])]
  public function addComment() {
    $data = $this->getJson();

    foreach (['message_id', 'content'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $dto = new CommentCreateDTO();
    $dto->user_id = $_SESSION['id'];
    $dto->message_id = (int)$data['message_id'];
    $dto->content = (string)$data['content'];
    $id = CommentRepository::getInstance()->addComment($dto);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $id]);
  }
}