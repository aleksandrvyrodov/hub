<?php

namespace MIT;

class Chain
{
  private object $Object;

  public function __construct(object $Object)
  {
    $this->Object = $Object;
  }

  public function __call($method, $arguments)
  {
    $this
      ->Object
      ->{$method}(...$arguments);
    return $this;
  }
}