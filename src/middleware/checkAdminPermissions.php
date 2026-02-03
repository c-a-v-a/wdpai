<?php

require_once 'AdminOnly.php';

function checkAdminPermissions(object $controller, string $method): bool {
  $reflection = new ReflectionClass($controller);

  if ($reflection->hasMethod($method)) {
    $methodReflection = $reflection->getMethod($method);

    if (!empty($methodReflection->getAttributes(AdminOnly::class))) {
      return $_SESSION['admin'];
    }
  }

  return true;
}