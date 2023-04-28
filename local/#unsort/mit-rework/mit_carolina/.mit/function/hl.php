<?php

namespace MIT\Function\Hl;

function user_include_message(string $reason, &$file = false)
{
  $file = false;

  if (!\CModule::IncludeModule('highloadblock'))
    return '';



  $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(\MIT\HL\USER_INCLUDE_ID)->fetch();
  $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);

  $EntityHlBlock = $obEntity->getDataClass();

  $userFilter = ['UF_REASON' => $reason];

  $resData = $EntityHlBlock::getList([
    'select' => ['UF_MESS', 'UF_MESS_F'],
    'filter' => $userFilter,
    'order'  => ['ID' => 'DESC'],
  ]);

  if ($arItem = $resData->Fetch()) {
    if (
      1
      && (int)$arItem['UF_MESS_F']
      && ($path = \CFile::GetPath((int)$arItem['UF_MESS_F']))
      && file_exists(\MIT\PATH_ENV . $path)
    ) {
      $file = true;
      return file_get_contents(\MIT\PATH_ENV . $path);
    } else return $arItem['UF_MESS'];
  } else return '';
}
