<?php

require_once 'Repository.php';
require_once __DIR__.'/../data/Event.php';
require_once __DIR__.'/../data/EventCreateDTO.php';

class EventRepository extends Repository {
  public function creteEvent(EventCreateDTO $event): int {
    $conn = $this->database->getConnection();
    $sql = "INSERT INTO events (created_by, title, description, event_date)
            VALUES (:created_by, :title, :description, :event_date)
            RETURNING id";
    $stmt = $conn->prepare($sql);
    
    $formattedDate = new DateTime($event->event_date)->format('Y-m-d H:i:s');

    $stmt->execute([
      ':created_by' => $event->created_by,
      ':title' => $event->title,
      ':description' => $event->description,
      ':event_date' => $formattedDate
    ]);

    return (int)$stmt->fetchColumn();
  }

  public function getEvents(): array {
    $conn = $this->database->getConnection();
    $sql = "SELECT id, title, description, event_date, creator_first_name, creator_surname, users 
            FROM events_with_users
            WHERE EXTRACT(MONTH FROM event_date) = EXTRACT(MONTH FROM NOW())
            AND EXTRACT(YEAR FROM event_date) = EXTRACT(YEAR FROM NOW())";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn($row) => Event::fromDbRow($row), $rows);
  }
}