<?php

use MIT\Tool\ScssCompiler;

ScssCompiler::$DEV_MODE = true;
ScssCompiler::$MAP_FILE = false;

return fn(
  $from_path,
  $to_path,
  $list_scss_files,
  $silent = true
) => (new ScssCompiler(
  from_path: $from_path,
  to_path: $to_path ,
  root_path: $_SERVER['DOCUMENT_ROOT'],
  list_scss_files: $list_scss_files,
  union: ScssCompiler::UINION_MODE_ON
))($silent);