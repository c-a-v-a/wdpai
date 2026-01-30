<?php

require_once __DIR__.'/../../data/EventCreateDTO.php';
require_once __DIR__.'/../../repositories/EventRepository.php';

class EventApiController {
  private static ?EventApiController $instance = null;

  private function __construct() {}

  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new EventApiController();
    }

    return self::$instance;
  }

  #[AllowedMethods(['GET', 'POST'])]
  public function index() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->addEvent();
    } else {
      $this->getEvents();
    }
  }

  private function addEvent() {
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

    foreach (['created_by', 'title', 'description', 'event_date'] as $field) {
      if (!isset($data[$field])) {
        http_response_code(400);
        throw new RuntimeException("Missing field $field");
      }
    }

    $dto = new EventCreateDTO();
    $dto->created_by = (int)$data['created_by'];
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