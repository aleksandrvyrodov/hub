<?php

namespace MIT\Tool;

class Chain
{
  private object $Object;
  private bool $cond = true;

  public function __construct(object $Object)
  {
    $this->Object = $Object;
  }

  public function __call($method, $arguments)
  {
    if ($this->cond)
      $this
        ->Object
        ->{$method}(...$arguments);
    $this->_COND_();
    return $this;
  }

  public function _COND_(bool $cond = true)
  {
    $this->cond = $cond;
    return $this;
  }
}
