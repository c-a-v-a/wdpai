<?php

class Comment implements JsonSerializable {
  public string $content;
  public string $first_name;
  public string $surname;
  public string $created_at;

  public function jsonSerialize(): array {
    return [
      'content' => $this->content,
      'first_name' => $this->first_name,
      'surname' => $this->surname,
      'created_at' => $this->created_at,
    ];
  }
}