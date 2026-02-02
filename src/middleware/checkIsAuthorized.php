<?php

require_once 'PublicRoute.php';

function checkIsAuthorized(object $controller, string $method): bool {
  $reflection = new ReflectionClass($controller);

  if ($reflection->hasMethod($method)) {
    $methodReflection = $reflection->getMethod($method);

    if (!empty($methodReflection->getAttributes(PublicRoute::class))) {
      return true;
    }
  }

  return isset($_SESSION['id']);
}