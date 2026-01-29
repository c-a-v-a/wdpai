<?php

require_once 'Repository.php';
require_once __DIR__.'/../data/CommentCreateDTO.php';

class CommentRepository extends Repository {
  public function addComment(CommentCreateDTO $comment) {
    $conn = $this->database->getConnection();
    $sql = "INSERT INTO comment (user_id, message_id, content)
            VALUES (:user_id, :message_id, :content)
            RETURNING id";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([
      ':user_id' => $comment->user_id,
      ':message_id' => $comment->message_id,
      ':content' => $comment->content,
    ]);

    return (int)$stmt->fetchColumn();
  }
}