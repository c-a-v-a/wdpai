<?php

require_once 'Repository.php';
require_once __DIR__.'/../data/User.php';
require_once __DIR__.'/../data/UserCreateDTO.php';
require_once __DIR__.'/../data/UserLoginDTO.php';

class UserRepository extends Repository {
  public function addUser(UserCreateDTO $user): int {
    $conn = $this->database->getConnection();
    $sql = "INSERT INTO users (first_name, surname, email, password)
            VALUES (:first_name, :surname, :email, :password)
            RETURNING id";
    $stmt = $conn->prepare($sql);

    $hashedPass = password_hash($user->password, PASSWORD_BCRYPT);
    
    $stmt->execute([
      ':first_name' => $user->first_name,
      ':surname' => $user->surname,
      ':email' => $user->email,
      ':password' => $hashedPass
    ]);

    return (int)$stmt->fetchColumn();
  }

  public function getUsers(): array {
    $conn = $this->database->getConnection();
    $sql = "SELECT id, email, first_name, surname, active FROM users ORDER BY id";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_CLASS, User::class);
  }

  public function getUser(string $email): object {
    $conn = $this->database->getConnection();
    $sql = "SELECT id, email, password FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute([ 'email' => $email ]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, UserLoginDTO::class);

    return $stmt->fetch();
  }

  public function enableUser(int $userId): bool {
    $conn = $this->database->getConnection();
    $sql = "UPDATE users SET active = TRUE WHERE id = :id AND active = FALSE";
    $stmt = $conn->prepare($sql);

    $stmt->execute(['id' => $userId]);

    return $stmt->rowCount() === 1;
  }

  public function disableUser(int $userId): bool {
    $conn = $this->database->getConnection();
    $sql = "UPDATE users SET active = FALSE WHERE id = :id AND active = TRUE";
    $stmt = $conn->prepare($sql);

    $stmt->execute(['id' => $userId]);

    return $stmt->rowCount() === 1;
  }
}