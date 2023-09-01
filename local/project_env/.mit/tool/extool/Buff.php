<?php

namespace MIT\Tool;

class Buff
{
  static private array $listInst = [];

  static public function Init(string $key): void
  {
    static::$listInst[$key] ??= new static();
  }

  private function __construct()
  {
    ob_start();
  }

  public function __destruct()
  {
    ob_end_clean();
  }

  static public function Last(string $key): string|false
  {
    if (!static::$listInst[$key])
      return false;

    $buff = ob_get_contents();

    static::$listInst[$key] = null;
    unset(static::$listInst[$key]);

    return $buff;
  }
}
