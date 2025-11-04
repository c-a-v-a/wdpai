<?php

require_once 'src/Router.php';

$path = trim($_SERVER['REQUEST_URI'], '/');
$path = parse_url($path);

if (array_key_exists("query", $path)) {
  Router::run($path["path"], $path["query"]);
} else {
  Router::run($path["path"], "");
}
