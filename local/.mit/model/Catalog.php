<?php

namespace MIT\Model;

use MIT\Loader;

class Catalog implements IIncludeDependencies, ISingleton
{
  private static $Catalog;

  public static function Dep(Loader $Loader): bool
  {
    $res = 1
      && $Loader->loadModule('catalog');

    return $res;
  }

  public static function Init(): ISingleton
  {
    if (empty(self::$Catalog))
      self::$Catalog = new self();

    return self::$Catalog;
  }

  public function __construct()
  {
    echo "hello";
  }
}
