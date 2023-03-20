<?php

use MIT\Loader;

include __DIR__ . '/bitrix/modules/main/include/prolog_before.php';

/**
 * @var Catalog $Catalog
 */
$Catalog = Loader::Init()
  ->loadModelInit('Catalog');
