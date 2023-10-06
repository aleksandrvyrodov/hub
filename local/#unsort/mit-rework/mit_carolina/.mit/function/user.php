<?php

namespace MIT\Function\User;


function filterByManagerForUser($name_filter_merge = null): array
{
  global $USER;
  $USER_ID = (int)$USER->GetID();
  $MANAGER_ID = 0;

  $fn_rand_id_manager = function (): int {
    $res = \CIblockElement::GetList(
      ['RAND' => 'ASC'],
      [
        'SECTION_ID' => 0,
        'IBLOCK_ID' => \MIT\User\MANAGER_IBLOCK_ID,
        'ACTIVE' => 'Y',
        'PROPERTY_SHOW_SIDE_BLOCK_VALUE' => 'Y'
      ],
      false,
      false,
      ['ID']
    );

    if ($ar_res = $res->GetNext())
      return (int)$ar_res['ID'];
    else
      return 0;
  };

  if ($USER_ID) {
    $arRes = \CUser::GetList($by, $desc, ["ID" => (int)$USER_ID], ['SELECT' => [\MIT\User\MANAGER_UF_NAME]]);

    if ($res = $arRes->Fetch())
      $MANAGER_ID = (int)$res[\MIT\User\MANAGER_UF_NAME];
    else
      $MANAGER_ID = $fn_rand_id_manager();
  } else {
    $MANAGER_ID = $fn_rand_id_manager();
  }

  return array_merge(
    empty($name_filter_merge) ? [] :  (array)$GLOBALS[$name_filter_merge],
    ['ID' => $MANAGER_ID]
  );
}

# \MIT\Function\User\isAuth()
function isAuth()
{
  global $USER;
  return $USER->IsAuthorized();
}

function ConfurmEmail(&$arFields){

  file_put_contents(__DIR__ . '/fields.txt', var_export($arFields, true).PHP_EOL.PHP_EOL, FILE_APPEND);

  return $arFields;
}