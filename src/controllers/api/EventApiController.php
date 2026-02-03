<?php

require_once 'ApiController.php';
require_once __DIR__.'/../../data/EventCreateDTO.php';
require_once __DIR__.'/../../data/EventJoinDTO.php';
require_once __DIR__.'/../../repositories/EventRepository.php';

class EventApiController extends ApiController {
  #[AllowedMethods(['GET', 'POST'])]
  public function index() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->addEvent();
    } else {
      $this->getEvents();
    }
  }

  #[AllowedMethods(['POST'])]
  public function joinEvent(array $params) {
    if (!array_key_exists('event_id', $params)) {
      http_response_code(400);
      throw new RuntimeException('Invalid query params');
    }

    $dto = new EventJoinDTO();
    $dto->event_id = $params['event_id'];
    $dto->user_id = $_SESSION['id'];

    EventRepository::getInstance()->joinEvent($dto);

    echo json_encode(['success' => true]);
  }

  private function addEvent() {
    $data = $this->getJson();

    foreach (['title', 'description', 'event_date'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $dto = new EventCreateDTO();
    $dto->created_by = $_SESSION['id'];
    $dto->title = (string)$data['title'];
    $dto->description = (string)$data['description'];
    $dto->event_date = (string)$data['event_date'];
    $id = EventRepository::getInstance()->addEvent($dto);

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'id' => $id]);

    return;
  }

  private function getEvents() {
    $events = EventRepository::getInstance()->getEvents();

    header('Content-Type: application/json');
    echo json_encode($events);
    
    return;
  }
}