<?php

namespace MIT\App\Ajax;

use MIT\App\Ajax\Core\AnswerPack;
use MIT\App\Ajax\Core\Handler;
use const MIT\PATH_ENV;

require_once __DIR__ . '/inf.php';

# \--------------------------------------------------
require_once PATH_ENV . '/bitrix/modules/main/include.php';
# /--------------------------------------------------

try {
  $Handler = Handler::Init();
  $Output = $Handler->execBurn();

  #
} catch (\Throwable $th) {
  $Output = Handler::errorAnswerPack($th);
} finally {
  // header_remove();
  header('Content-Type: application/json');

  switch (true) {
    case $Output instanceof AnswerPack:
      echo $Output;
      break;
    case !empty($Output):
      echo Handler::errorAnswerPack(new \Exception('Unknown answer', 1));
      break;
    default:
      header($_SERVER["SERVER_PROTOCOL"] . " 418 I'm a teapot");
  }
}
