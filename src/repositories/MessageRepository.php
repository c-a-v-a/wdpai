<?php

require_once 'Repository.php';
require_once __DIR__.'/../data/Message.php';
require_once __DIR__.'/../data/MessageCreateDTO.php';

class MessageRepository extends Repository {
  public function addMessage(MessageCreateDTO $message): int {
    $conn = $this->database->getConnection();
    $sql = "INSERT INTO messages (user_id, title, content)
            VALUES (:user_id, :title, :content)
            RETURNING id";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([
      ':user_id' => $message->user_id,
      ':title' => $message->title,
      ':content' => $message->content,
    ]);

    return (int)$stmt->fetchColumn();
  }

  public function getMessages(): array {
    $conn = $this->database->getConnection();
    $sql = "SELECT id, title, content, created_at, author_first_name, author_surname, comments 
            FROM messages_with_author_and_comments";
            // WHERE created_at >= NOW() - INTERVAL '3 days'";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn($row) => Message::fromDbRow($row), $rows);
  }

  public function getNewMessages(): array {
    $conn = $this->database->getConnection();
    $sql = "SELECT id, title, content, created_at, author_first_name, author_surname, comments 
            FROM messages_with_author_and_comments
            WHERE created_at >= NOW() - INTERVAL '1 days'";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map(fn($row) => Message::fromDbRow($row), $rows);
  }
}