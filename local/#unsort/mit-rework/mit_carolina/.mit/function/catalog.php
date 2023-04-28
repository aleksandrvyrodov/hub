<?php

namespace MIT\Function\Catalog;

use MIT\Loader;
use MIT\Model\FavoriteProduct;
use MIT\Model\ExchangeMod1C;

function listFavoriteProduct(): array
{
  global ${__FUNCTION__ . ':Storage'};

  if (empty(${__FUNCTION__ . ':Storage'})) {
    /**
     * @var FavoriteProduct $FavoriteProduct
     */
    $FavoriteProduct = Loader::Init()
      ->loadModelInit('FavoriteProduct');
    ${__FUNCTION__ . ':Storage'} = $FavoriteProduct->initStorage();
  }

  return ${__FUNCTION__ . ':Storage'}->getList();
}

function alertSectionTrigerMarkup(int $SECTION_ID = 0, array|false $PROP_VAL = false)
{
  $_SESSION['SECTION_ALERT'] ??= [];

  $arFilterPack = [
    'CODE' => $PROP_VAL ? 'PROP_VAL' : 'SECTION',
    'VAL'  => $PROP_VAL ? $PROP_VAL : [$SECTION_ID],
  ];

  $arFilter = [
    'IBLOCK_ID' => 39,
    'ACTIVE' => 'Y',
    'PROPERTY_' . $arFilterPack['CODE'] => $arFilterPack['VAL']
  ];

  $res = \CIBlockElement::GetList([], $arFilter);
  $markup = '';

  while ((bool)$arFilterPack['VAL'] && ($ob = $res->GetNextElement())) {
    $arFields = $ob->GetFields();

    ob_start();
?>
    <div class="callback-block animate-load font_upper_sm colored" data-event="jqm" data-name="section" data-param-type="section" data-param-code="<?= $arFilterPack['CODE'] ?>" data-param-val="<?= implode(':', $arFilterPack['VAL']) ?>">
      <i class="svg  svg-inline-info_big pull-left" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
          <defs>
            <style>
              .cls-1 {
                fill: #999;
                fill-rule: evenodd;
              }
            </style>
          </defs>
          <path data-name="Rounded Rectangle 937" class="cls-1" d="M1338,932a8,8,0,1,1-8,8A8,8,0,0,1,1338,932Zm0,2a6,6,0,1,1-6,6A6,6,0,0,1,1338,934Zm0,5a1,1,0,0,1,1,1v3a1,1,0,0,1-2,0v-3A1,1,0,0,1,1338,939Zm0-3a1,1,0,1,1-1,1A1,1,0,0,1,1338,936Z" transform="translate(-1330 -932)"></path>
        </svg></i>
      <span><?= $arFields['~NAME'] ?></span>
    </div>
<?

    $markup .= ob_get_contents();
    ob_end_clean();
  }

  if ($markup)
    return $markup;
  else false;
  /* else
    return false; */
}

function spoofAssociatedProp(&$arResult, &$arParams)
{
  if (empty($arResult['PROPERTIES'][\MIT\Catalog\NAME_PROP_ASSOCIATED]['VALUE']))
    return;

  $filter = [
    "IBLOCK_ID" => $arResult['ORIGINAL_PARAMETERS']['IBLOCK_ID'],
    "XML_ID" => $arResult['PROPERTIES'][\MIT\Catalog\NAME_PROP_ASSOCIATED]['VALUE'],
    'SECTION_GLOBAL_ACTIVE' => 'Y'
  ];

  $list_ProductID = [];

  $res = \CIblockElement::GetList([], $filter, false, false, ["ID"]);

  while ($ID = (int)$res->fetch()['ID'])
    $list_ProductID[] = $ID;

  if (!empty($list_ProductID)) {
    $arParams['LINKED_FILTER_BY_PROP']['ASSOCIATED']
      = $arResult['ORIGINAL_PARAMETERS']['LINKED_FILTER_BY_PROP']['ASSOCIATED']
      = $arResult['ORIGINAL_PARAMETERS']['LINKED_FILTER_BY_PROP']['ASSOCIATED']
      = $arResult['PROPERTIES']['ASSOCIATED']['~VALUE']
      = $arResult['PROPERTIES']['ASSOCIATED']['VALUE']
      = $list_ProductID;
  }
}

function modifyProductByExchange1C(...$args)
{
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
    echo $th->getMessage() . PHP_EOL;
  }
}
