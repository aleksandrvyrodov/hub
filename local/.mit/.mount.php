<?php

use MIT\App\Core\Autoloader;

require_once __DIR__ . '/.defined.php';
require MIT\PATH_APP . '/.core/Autoloader.php';

global $MITLoader;

(new Autoloader)
  ->addNamespace('MIT', MIT\PATH_ROOT)
  ->register();

$MITLoader = \MIT\Loader::Init();
