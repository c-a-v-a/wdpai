<?php

require_once 'Comment.php';

class Message implements JsonSerializable {
  public int $id;
  public string $title;
  public string $content;
  public string $created_at;
  public string $author_first_name;
  public string $author_surname;
  public array $comments = [];

  public static function fromDbRow(array $row): self {
    $msg = new self();
    $msg->id = $row['id'];
    $msg->title = $row['title'];
    $msg->content = $row['content'];
    $msg->created_at = $row['created_at'];
    $msg->author_first_name = $row['author_first_name'];
    $msg->author_surname = $row['author_surname'];
    $commentsData = json_decode($row['comments'], true) ?? [];

    foreach ($commentsData as $c) {
      $comment = new Comment();
      $comment->content = $c['content'];
      $comment->first_name = $c['first_name'];
      $comment->surname = $c['surname'];
      $comment->created_at = $c['created_at'];

      $msg->comments[] = $comment;
    }

    return $msg;
  }

  public function jsonSerialize(): array {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'content' => $this->content,
      'created_at' => $this->created_at,
      'author_first_name' => $this->author_first_name,
      'author_surname' => $this->author_surname,
      'comments' => $this->comments,
    ];
  }
}