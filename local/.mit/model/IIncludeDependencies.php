<?php

namespace MIT\Model;

use MIT\Loader;

interface IIncludeDependencies
{
  public static function Dep(Loader $loader): bool;
}
