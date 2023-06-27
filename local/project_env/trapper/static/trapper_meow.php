<?php

use MIT\Tool\Trapper;
use MIT\Tool\Invider;

require_once __DIR__ . '/../tool/Trapper.php';
require_once __DIR__ . '/../tool/Invader.php';


Invider::InitOnce(flags: PHP_OUTPUT_HANDLER_FLUSHABLE);

$Trapper = new class() extends Trapper
{
  public function s_exit()
  {
    Invider::SetJailCallbak(
      function ($_, $b) {

        if (
          0
          || stripos($this->storage['HEADERS']['Referer'], 'accesson.php') !== false
          || stripos($this->storage['SERVER']['REQUEST_URI'], 'accesson.php') !== false
        ) {
          if (!headers_sent()) {
            header_remove();
            header('Content-Type: text/plain; charset=utf-8');
            header($_SERVER["SERVER_PROTOCOL"] . " 418 I'm a teapot");
            $_ = 'Hihihi Meow (^・ω・^ )';
          }
        }

        $this
          // ->s_session()
          ->s_content($_, 'OUTPUT', 'html')
          ->s_headers_response()
          ->save($this::FORCE_SAVE)
          #
        ;

        return $_;
      },
      $this
    );

    return $this;
  }
};


$Trapper
  ->setSettings(
    path: '/var/www/u0490708/data/www/public_html/.souls',
    file: Trapper::FILE_PHP,
    mount: true
  )
  ->catchManualRequest(
    $Trapper->s_headers(...),
    $Trapper->s_server(...),
    $Trapper->s_get(...),
    $Trapper->s_post(...),
    $Trapper->s_files(...),
    $Trapper->s_request(...),
    $Trapper->s_input(...),
    $Trapper->s_cookie(...),

    $Trapper->s_exit(...)
  )
  #
;
