<?php

namespace MIT\Model;

use MIT\Loader;

interface ISingleton
{
  public static function Init(): ISingleton;
}
