<?php

namespace MIT\Tool;

use const MIT\PATH_ROOT;

class Logger implements ILoggerFile
{
  private \SplFileObject $SplFileObject;

  public function __construct(string $filename, bool $replace = false)
  {
    $this->SplFileObject = new \SplFileObject(PATH_ROOT . '/.term/' . $filename, $replace ? 'w' : 'a');
  }

  public function Write(string $data)
  {
    return $this->SplFileObject->fwrite($data);
  }

  public function Rewind()
  {
    return $this->SplFileObject->rewind();
  }
}
