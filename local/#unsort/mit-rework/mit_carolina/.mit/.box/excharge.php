<?php

#region Include
use MIT\Model\ParamCopyIBlock;
use MIT\Model\WorkshopIBlock;
use MIT\Model\WorkshopIBlockCatalog;
use MIT\Model\WorkshopIBlockElementCollector;
use MIT\Model\WorkshopIBlockElement;
use MIT\Model\WorkshopIBlockProduct;
use MIT\Model\WorkshopUnity;
use MIT\Tool\Logger;

use const MIT\SHELL\LAST_MOD_TIME;

# \--------------------------------------------------
require_once __DIR__ . '/../../bitrix/modules/main/include/prolog_before.php';
# /--------------------------------------------------
#endregion

# 11ea

$XML_ID = 'b9bfdac3-0e06-11e6-939f-005056ab7321';
$Result = \Bitrix\Iblock\ElementTable::getList([
  'select' => ['*'],
  'filter' => ['IBLOCK_ID' => 26, 'XML_ID' => $XML_ID],
]);

// $iblockFields = \CIBlock::GetArrayByID(26);


/* $q = new \Bitrix\Main\Entity\Query(\Bitrix\Iblock\PropertyTable::getEntity());
$q
  ->setSelect(['*'])
  ->setFilter([
    'IBLOCK_ID' => 26,
    'CODE' => 'PAZ'
  ])
  ->setOrder(['SORT' => 'ASC'])
  #
; */



// print_r($q->exec()->fetchAll());
// print_r($iblockFields);
print_r([
  'XML_ID' => $XML_ID,
  'Result' => $Result->fetchAll(),
]);
