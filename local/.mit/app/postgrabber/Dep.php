<?php

namespace MIT\App\Postgrabber;

use const MIT\PATH_ENV;

final class Dep{
  static public function Load (){
    require_once __DIR__ . '/inf.php';

    # \--------------------------------------------------
    require_once PATH_ENV . '/bitrix/modules/main/include/prolog_before.php';
    # /--------------------------------------------------
  }
}