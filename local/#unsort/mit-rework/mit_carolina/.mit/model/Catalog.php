<?php

namespace MIT\Model;

use MIT\Loader;

class Catalog implements IIncludeDependencies
{

  public static function Dep(Loader $Loader): bool
  {
    $res = 1
      && $Loader->loadModule('catalog');

    return $res;
  }

  public function __construct()
  {
    echo "hello";
  }
}
