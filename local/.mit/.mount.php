<?php

/**
 * mount end to bitrix/modules/main/include/prolog_before.php
 * to end
 * <?php require_once($_SERVER["DOCUMENT_ROOT"] . "/.mit/.mount.php"); ?>
 */

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

!defined('MIT\\MANUAL_ASSEMBLY')
  && (new Main)
  ->includeLibAll();

$MITLoader = Loader::Init();
