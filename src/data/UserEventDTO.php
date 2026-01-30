<?php

class UserEventDTO implements JsonSerializable {
  public int $id;
  public string $first_name;
  public string $surname;

  public function jsonSerialize(): array {
    return [
      'id' => $this->id,
      'first_name' => $this->first_name,
      'surname' => $this->surname,
    ];
  }
}