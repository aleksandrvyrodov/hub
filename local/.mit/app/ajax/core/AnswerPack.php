<?php

namespace MIT\App\Ajax\Core;

class AnswerPack
{
  const STATUS = 'status';
  const DATA = 'data';
  const REASON = 'reason';
  const MESSAGE = 'message';

  const STATUS_ERR = 'ERR';
  const STATUS_OK = 'OK';

  public function __construct($clean = false)
  {
    if (!$clean) {
      $this->{self::STATUS} = self::STATUS_ERR;
      $this->{self::MESSAGE} = 'Empty answer';
    }
  }

  public function setProp(string $prop, $value)
  {
    $this->{$prop} = $value;
    return $this;
  }

  public function delProp(string $prop)
  {
    unset($this->{$prop});
    return $this;
  }

  public function __toString()
  {
    return json_encode($this);
  }
}
