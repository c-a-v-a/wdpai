<?php

require_once 'UserEventDTO.php';

class Event {
  public int $id;
  public string $title;
  public string $description;
  public string $event_date;
  public string $creator_first_name;
  public string $creator_surname;
  public array $users = [];

  public static function fromDbRow(array $row): self {
    $event = new self();
    $event->id = $row['id'];
    $event->title = $row['title'];
    $event->description = $row['description'];
    $event->event_date = $row['event_date'];
    $event->creator_first_name = $row['creator_first_name'];
    $event->creator_surname = $row['creator_surname'];
    $usersData = json_decode($row['users'], true) ?? [];

    foreach ($usersData as $u) {
      $user = new UserEventDTO();
      $user->id = $u['id'];
      $user->first_name = $u['first_name'];
      $user->surname = $u['surname'];

      $event->users[] = $user;
    }

    return $event;
  }
}