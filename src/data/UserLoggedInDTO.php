<?php

class UserLoggedInDTO {
  public int $id;
  public string $email;
  public string $password;
  public bool $admin;
  public bool $active;
}