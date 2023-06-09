<?php

if (1
  && @$_GET['x-mode'] == 'frame'
  && !empty($_GET['x-script'])
  && (($php = false)
    || ($include = realpath(__DIR__ . '/' .  $_GET['x-script']))
    || ($include = $php = realpath(__DIR__ . '/' .  $_GET['x-script'] . '.php'))
  )
  && !empty(explode(__DIR__, $include)[1])
  && file_exists($include)
) {

  header('Content-type: text/html');
  require_once __DIR__ . '/.dev/tool.php';

  if ($php) {
    require_once __DIR__ . '/.dev/template.php';
  } elseif (is_file(($include .= '/index.php'))) {
    require_once __DIR__ . '/.dev/template.php';
  } else {
    $answer = $_SERVER["SERVER_PROTOCOL"] . " 404 Not Found";
    header($answer);
    http_response_code(404);
    include $_SERVER['DOCUMENT_ROOT'] . '/.mit/static/404.php';
  }

  #
} else {
  $answer = $_SERVER["SERVER_PROTOCOL"] . " 418 I'm a teapot";

  header($answer);
  echo $answer;
}
