<?php

use MIT\Loader;
use MIT\Model\FavoriteProduct;
use MIT\Model\FavoriteProduct\Storage;

define('MIT\\MANUAL_ASSEMBLY', true);

include __DIR__ . '/bitrix/modules/main/include/prolog_before.php';

/**
 * @var FavoriteProduct $FavoriteProduct
 */
$FavoriteProduct = Loader::Init()
  ->loadModelInit('FavoriteProduct');


$Storage = &$FavoriteProduct->initStorage(FavoriteProduct::MODE_COOKIE);
