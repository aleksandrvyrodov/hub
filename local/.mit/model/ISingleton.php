<?php

namespace MIT\Model;

interface ISingleton
{
  public static function Init(): ISingleton;
}
