<?php

namespace MIT\Tool;

interface ILoggerFile{
  public function Write(string $data);
  public function Rewind();
}