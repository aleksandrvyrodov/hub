<?php

use MIT\Loader;
use MIT\Model\ExchangeMod1C;

# \--------------------------------------------------
require_once __DIR__ . '/../../bitrix/modules/main/include/prolog_before.php';
# /--------------------------------------------------


try {
  $iblockIdRoot = \MIT\Catalog\MAIN_CATALOG_ID;
  $iblockIdTarget = \MIT\Catalog\SKU_CATALOG_ID;

  $COUNTER = 0;
  $dbItems = \Bitrix\Iblock\ElementTable::getList([
    'select' => ['NAME', 'CNT'],
    'filter' => ['IBLOCK_ID' => $iblockIdRoot, '>CNT' => 1],
    'group' => ['NAME'],
    'runtime' => array(
      new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
    )
  ]);

  while ($row = $dbItems->fetch()) {
    $dbItems_l1 = \Bitrix\Iblock\ElementTable::getList([
      'select' => ['ID', 'XML_ID'],
      'filter' => ['IBLOCK_ID' => $iblockIdRoot, 'NAME' => $row['NAME']],
    ]);

    $elementIdMain = 0;
    $elementBitMain = 0b11;

    while ($row = $dbItems_l1->fetch()) {
      $elementIdRoot = $row['ID'];
      $elementXMLRoot = $row['XML_ID'];

      if ($elementIdMain === 0)
        $elementIdMain = $elementIdRoot;
      else
        $elementBitMain = 0b01;

      $EM1C = ExchangeMod1C::Init();

      if (ExchangeMod1C::CheckChangeElement($iblockIdTarget, $elementIdRoot)) {
        $COUNTER++;

        $EM1C
          ->checked($EM1C::CopyElement($elementIdRoot, $iblockIdTarget, $elementBitMain))
          ->checked($EM1C::CopyElementProperty($elementIdRoot, $iblockIdRoot, ExchangeMod1C::LastElementId(), $iblockIdTarget));

        $elementIdTarget = ExchangeMod1C::LastElementId();
        ExchangeMod1C::addProductSKU($elementIdMain, $elementIdTarget);
        ExchangeMod1C::CopyProductMain($elementIdRoot, $elementIdTarget);
        ExchangeMod1C::CopyProductStore($elementIdRoot, $elementIdTarget);
        ExchangeMod1C::CopyProductPrice($elementIdRoot, $elementIdTarget);
      }

      if ($elementIdRoot != $elementIdMain)
        ExchangeMod1C::DisableProduct($elementIdRoot);
    }
  }

  echo 'success' . PHP_EOL;
  echo "Rework $COUNTER products." . PHP_EOL;

  #
} catch (\Throwable $th) {
  echo 'error' . PHP_EOL;
  echo $th->__toString() . PHP_EOL;
}