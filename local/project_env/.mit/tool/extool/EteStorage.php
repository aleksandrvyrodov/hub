<?php

namespace MIT\Tool;

use stdClass;

class EteStorage extends stdClass
{
  static private EteStorage $Inst;
  static private array $listInst = [];

  static public function Inst(string|false $key = false): self
  {
    if ($key === false) {
      static::$Inst ??= new static();
      return static::$Inst;
    } else {
      static::$listInst[$key] ??= new static();
      return static::$listInst[$key];
    }
  }

  private function __construct()
  {
  }

  public function __invoke(string $prop, $val, &$old_val = null)
  {
    $old_val = $this->{$prop};
    $this->{$prop} = $val;
    return $this->{$prop};
  }

  static public function Unset(string $key): void
  {
    static::$listInst[$key] = null;
    unset(static::$listInst[$key]);
  }
}
