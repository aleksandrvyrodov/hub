<?php

print_r(\Bitrix\Iblock\IblockTable::getList([
  'select' => ['*'],
  'filter' => ['ID' => 26]
])->fetch());
print_r(\Bitrix\Iblock\IblockGroupTable::getList([
  'select' => ['*'],
  'filter' => ['IBLOCK_ID' => 26]
])->fetchAll());


//-------------------------------------------------------------

$idArray = [];
$resultObj = ElementTable::getList([
  'select' => ['ELEMENT_PROPERTY.VALUE'],
  'filter' => ['IBLOCK_ID' => 1, "IBLOCK_SECTION_ID" => 1, 'ACTIVE'=>'Y'],
  'runtime' => [
    new Reference(
      'ELEMENT_PROPERTY',
      ElementPropertyTable::class,
      Join::on('this.ID', 'ref.IBLOCK_ELEMENT_ID')
    ),
  ],
]);
while ($rowArray = $resultObj->fetch()) {
  $idArray[] = $rowArray['IBLOCK_ELEMENT_ELEMENT_PROPERTY_VALUE'];
}

$resultArray = PropertyEnumerationTable::getList([
  'select' => ['VALUE'],
  'filter' => ['ID' => array_unique($idArray)],
  ])->fetchAll();

  //-------------------------------------------------------------
  //-------------------------------------------------------------
  //-------------------------------------------------------------
  //-------------------------------------------------------------
  //-------------------------------------------------------------