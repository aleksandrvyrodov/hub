<?php

/**
 * mount end to local/php_interface/init.php
 * to start
 * @include_once __DIR__ . '/../../bitrix/php_interface/init.php';
 * require_once __DIR__ . '/../../.mit/.mount.php';
 */

use MIT\App\Core\Autoloader;
use MIT\App\Core\Main;
use MIT\Loader;

require_once __DIR__         . '/.defined.php';
require_once MIT\PATH_APP    . '/core/Autoloader.php';
require_once MIT\PATH_VENDOR . '/autoload.php';

require_once MIT\PATH_FN     . '/junkyard.php';
require_once MIT\PATH_FN     . '/catalog.php';
require_once MIT\PATH_FN     . '/hl.php';
require_once MIT\PATH_FN     . '/user.php';

global $MITLoader;

(new Autoloader)
  ->addNamespace('MIT', MIT\PATH_ROOT)
  ->register();

!defined('MIT\\MANUAL_ASSEMBLY')
  && (new Main)
  ->includeLibAll();

$MITLoader = Loader::Init();