<?php

use MIT\Static\Trapper\Trapper as TrapperTrapper;
use MIT\Tool\Trapper;

define('MIT\\MANUAL_ASSEMBLY', true);

require_once __DIR__ . '/../.mount.php';


Trapper::InitOnce(Trapper::CATCH_BEFORE);
// Trapper::InitOnce();

$Trapper = new class() extends TrapperTrapper{
  public function s_exit(){
    Trapper::SetJailCallbak(
      function($_, $b){
        $this->storage['MESSAGE'] = explode(PHP_EOL, trim($b . $_, PHP_EOL));
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
    /* $Trapper->s_headers(...),
    $Trapper->s_server(...),
    $Trapper->s_get(...),
    $Trapper->s_post(...),
    $Trapper->s_files(...),
    $Trapper->s_request(...),
    $Trapper->s_input(...),
    $Trapper->s_session(...),
    $Trapper->s_cookie(...), */
    $Trapper->s_exit(...)
  );




echo ''
  . 'success' . PHP_EOL
  . 'Dev MIT mode' . PHP_EOL;

exit();