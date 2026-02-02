<?php

require_once 'ApiController.php';
require_once __DIR__.'/../../data/MessageCreateDTO.php';
require_once __DIR__.'/../../repositories/MessageRepository.php';

class MessageApiController extends ApiController {
  #[AllowedMethods(['GET', 'POST'])]
  public function index() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->addMessage();
    } else {
      $this->getMessages();
    }
  }

  private function addMessage() {
    $data = $this->getJson();

    foreach (['user_id', 'title', 'content'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $dto = new MessageCreateDTO();
    $dto->user_id = (int)$data['user_id'];
    $dto->title = (string)$data['title'];
    $dto->content = (string)$data['content'];
    $id = MessageRepository::getInstance()->addMessage($dto);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $id]);

    return;
  }

  private function getMessages() {
    $messages = MessageRepository::getInstance()->getMessages();

    header('Content-Type: application/json');
    echo json_encode($messages);
    
    return;
  }
}