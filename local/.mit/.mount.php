<?php

use MIT\App\Core\Autoloader;
use MIT\App\Core\Main;
use MIT\Loader;

require_once __DIR__         . '/.defined.php';
require_once MIT\PATH_APP    . '/core/Autoloader.php';
require_once MIT\PATH_VENDOR . '/autoload.php';

global $MITLoader;

(new Autoloader)
  ->addNamespace('MIT', MIT\PATH_ROOT)
  ->register();

(new Main)
  ->includeLibAll();

$MITLoader = Loader::Init();
