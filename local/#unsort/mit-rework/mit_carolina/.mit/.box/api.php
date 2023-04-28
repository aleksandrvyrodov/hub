<?php

use MIT\Static\Trapper\Trapper as TrapperTrapper;
use MIT\Tool\Trapper;

define('MIT\\MANUAL_ASSEMBLY', true);

require_once __DIR__ . '/../.mount.php';


$Trapper = new class() extends TrapperTrapper{
  public function s_exit(){

    Trapper::InitOnce();
    Trapper::SetJailCallbak(
      function($_){
        $this->storage['MESSAGE'] = $_;
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
    $Trapper->s_get(...),
    $Trapper->s_exit(...)
  );




echo 'EMPTY';
exit();