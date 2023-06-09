<?php

use MIT\Static\Trapper\Trapper as TrapperTrapper;
use MIT\Tool\Trapper;

define('MIT\\MANUAL_ASSEMBLY', true);

require_once __DIR__ . '/../.mount.php';


Trapper::InitOnce();

$Trapper = new class() extends TrapperTrapper
{
  public function s_exit()
  {
    Trapper::SetJailCallbak(
      function ($_, $b) {

        $encode = mb_detect_encoding(string: $_, strict: true);

        session_start();
        $this->storage['SESSION--BX_CML2_EXPORT'] = $_SESSION["BX_CML2_EXPORT"] ?? NULL;
        $this->storage['MESSAGE'] = explode(PHP_EOL, trim(iconv('cp1251//IGNORE', 'utf-8//IGNORE', $_)));
        $this->save($this::FORCE_SAVE);
        return $_;
      },
      $this
    );


    return $this;
  }
};

$Trapper
  ->catchManualRequest(
    $Trapper->s_headers(...),
    // $Trapper->s_server(...),
    $Trapper->s_get(...),
    // $Trapper->s_post(...),
    // $Trapper->s_files(...),
    // $Trapper->s_request(...),
    // $Trapper->s_input(...),
    // $Trapper->s_cookie(...),

    $Trapper->s_exit(...)
  );
