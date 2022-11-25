<?php

namespace MIT\Model;

use MIT\Loader;

interface IIncludeDependencies
{
  public static function Dep(Loader $Loader): bool;
}
