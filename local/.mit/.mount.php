<?php

use MIT\App\Core\Autoloader;
use MIT\Loader;

require_once __DIR__         . '/.defined.php';
require_once MIT\PATH_APP    . '/.core/Autoloader.php';
require_once MIT\PATH_VENDOR . '/autoload.php';

global $MITLoader;

$Whoops = new \Whoops\Run;
$Whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$Whoops->register();

(new Autoloader)
  ->addNamespace('MIT', MIT\PATH_ROOT)
  ->register();

$MITLoader = Loader::Init();
