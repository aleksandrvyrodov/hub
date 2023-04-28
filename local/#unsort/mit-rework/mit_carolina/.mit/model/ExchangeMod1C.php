<?php

namespace MIT;

namespace MIT\Model;

use MIT\Loader;
use \Bitrix\Main\Result;
use \Bitrix\Main\Error;

use function MIT\Function\Junkyard\GUIDv4;

final class ExchangeMod1C implements IIncludeDependencies
{
  static private ExchangeMod1C $Inited;
  static private Result $Result;
  static private int $root_IBLOCK_ID = 0;
  static private int $target_IBLOCK_ID = 0;
  static private int $last_ELEMENT_ID = 0;

  #region INCAPS
  static function RootIblockId(): int
  {
    return self::$root_IBLOCK_ID;
  }
  static function TargetIblockId(): int
  {
    return self::$target_IBLOCK_ID;
  }
  static function LastElementId(): int
  {
    return self::$last_ELEMENT_ID;
  }
  #endregion

  public static function Dep(Loader $Loader): bool
  {
    $res = 1;

    return $res;
  }

  private function __construct()
  {
    self::$Result = new Result();
  }

  static public function Init()
  {
    self::$Inited = self::$Inited ?? new self();

    return self::$Inited;
  }

  public function checked($Result)
  {
    if (!(self::$Result = $Result)->isSuccess())
      throw new \Exception(implode(PHP_EOL, $Result->getErrorMessages()), 1);
    return $this;
  }

  static private function _ErrorGenerator($mes)
  {
    self::$Result->addError(new Error($mes));
    return self::$Result;
  }

  #region IBlock
  static private function _AddPropLinkSKU(int $iblockIdTarget, int $iblockIdRoot): int
  {
    $iblockPropertyNew = new \CIBlockProperty();

    $res = (int)$iblockPropertyNew->Add([
      'TIMESTAMP_X' => date('Y-m-d H:i:s'),
      'IBLOCK_ID' => (string) $iblockIdTarget,
      'LINK_IBLOCK_ID' => (string) $iblockIdRoot,
      'NAME' => 'Элемент каталога',
      'ACTIVE' => 'Y',
      'SORT' => '9999',
      'CODE' => 'CML2_LINK',
      'DEFAULT_VALUE' => '',
      'PROPERTY_TYPE' => 'E',
      'ROW_COUNT' => '1',
      'COL_COUNT' => '30',
      'LIST_TYPE' => 'L',
      'MULTIPLE' => 'N',
      'XML_ID' => 'CML2_LINK',
      'FILE_TYPE' => '',
      'MULTIPLE_CNT' => '5',
      'TMP_ID' => '',
      'WITH_DESCRIPTION' => 'N',
      'SEARCHABLE' => 'N',
      'FILTRABLE' => 'Y',
      'IS_REQUIRED' => 'N',
      'VERSION' => '1',
      'USER_TYPE' => 'SKU',
      'USER_TYPE_SETTINGS' => [
        'VIEW' => 'A',
        'SHOW_ADD' => 'N',
        'MAX_WIDTH' => '0',
        'MIN_HEIGHT' => '24',
        'MAX_HEIGHT' => '1000',
        'BAN_SYM' => ',;',
        'REP_SYM' => ' ',
        'OTHER_REP_SYM' => '',
        'IBLOCK_MESS' => 'N',
      ],
      'HINT' => '',
    ]);

    if (!$res)
      self::_ErrorGenerator('[#83] ' . $iblockPropertyNew->LAST_ERROR);

    return $res;
  }

  static public function CopyIBlock(int $iblockIdRoot, string $iblockTypeTarget): Result
  {
    if (empty($iblockIdRoot)) return self::_ErrorGenerator('[#44] Не указан инфоблок для копирования');
    if (empty($iblockTypeTarget)) return self::_ErrorGenerator('[#45] Не указан инфоблок для копирования');

    self::$root_IBLOCK_ID = $iblockIdRoot;

    $iblockFields = \CIBlock::GetArrayByID($iblockIdRoot);
    unset($iblockFields["ID"], $iblockFields["LID"]);
    $iblockFields["GROUP_ID"] = \CIBlock::GetGroupPermissions($iblockIdRoot);

    $iblockFields["NAME"] .= " (SKU)";
    $iblockFields["XML_ID"]  = 'C-' . $iblockFields["XML_ID"] . '#';
    $iblockFields["API_CODE"] = !$iblockFields["API_CODE"] ? '' :  'C-' . $iblockFields["API_CODE"];
    $iblockFields["IBLOCK_TYPE_ID"] = $iblockTypeTarget;


    $iblockSiteObject = \CIBlock::GetSite($iblockIdRoot);
    while ($iblockSite = $iblockSiteObject->Fetch())
      $iblockFields["LID"][] = $iblockSite['SITE_ID'];

    $iblockTarget = new \CIBlock();
    $iblockIdTarget = self::$target_IBLOCK_ID = (int)$iblockTarget->Add($iblockFields);

    if ($iblockIdTarget === 0)
      return self::_ErrorGenerator('[#77] ' . $iblockTarget->LAST_ERROR);

    return self::$Result;
  }

  static public function CopyIBlockProperty(int $iblockIdRoot = 0, int $iblockIdTarget = 0, ?\Closure $fn_filter = null): Result
  {
    is_null($fn_filter)
      && $fn_filter = fn ($key) => true;

    $iblockIdRoot = $iblockIdRoot ? $iblockIdRoot : self::$root_IBLOCK_ID;
    $iblockIdTarget = $iblockIdTarget ? $iblockIdTarget : self::$target_IBLOCK_ID;

    if (empty($iblockIdRoot)) return self::_ErrorGenerator('[#94] Не указан инфоблок для копирования');
    if (empty($iblockIdTarget)) return self::_ErrorGenerator('[#95] Не указан инфоблок для копирования');

    $iblockPropertyTarget = new \CIBlockProperty();
    $iblockProperties = \CIBlockProperty::GetList(
      ["sort" => "asc", "name" => "asc"],
      ["ACTIVE" => "Y", "IBLOCK_ID" => $iblockIdRoot]
    );

    while ($property = $iblockProperties->GetNext()) {
      if (!$fn_filter($property["CODE"]))
        continue;

      $property["IBLOCK_ID"] = $iblockIdTarget;
      unset($property["ID"]);

      if ($property["PROPERTY_TYPE"] === "L") {
        $propertyEnums = \CIBlockPropertyEnum::GetList(
          ["DEF" => "DESC", "SORT" => "ASC"],
          ["IBLOCK_ID" => $iblockIdRoot, "CODE" => $property["CODE"]]
        );
        while ($enumFields = $propertyEnums->GetNext()) {
          $property["VALUES"][] = [
            "XML_ID" => $enumFields["XML_ID"],
            "VALUE" => $enumFields["VALUE"],
            "DEF" => $enumFields["DEF"],
            "SORT" => $enumFields["SORT"]
          ];
        }
      }

      foreach ($property as $k => $v) {
        if (!is_array($v))  $property[$k] = trim($v);
        if ($k[0] === '~') unset($property[$k]);
      }

      $propertyCopy = $iblockPropertyTarget->Add($property);
      if ($propertyCopy === false)
        return self::_ErrorGenerator('[#134] ' . $iblockPropertyTarget->LAST_ERROR);

      #
    }

    return self::$Result;
  }

  static public function MountCatalog(int $iblockIdRoot = 0, int $iblockIdTarget = 0): Result
  {
    global $APPLICATION;

    $iblockIdRoot = $iblockIdRoot ? $iblockIdRoot : self::$root_IBLOCK_ID;
    $iblockIdTarget = $iblockIdTarget ? $iblockIdTarget : self::$target_IBLOCK_ID;

    if (empty($iblockIdRoot)) return self::_ErrorGenerator('[#194] Не указан инфоблок для копирования');
    if (empty($iblockIdTarget)) return self::_ErrorGenerator('[#195] Не указан инфоблок для копирования');

    if (!\CCatalog::GetByID($iblockIdTarget)) {
      $res = \CCatalog::Add([
        'IBLOCK_ID' => $iblockIdTarget,
      ]);

      if (!$res) {
        if ($ex = $APPLICATION->GetException())
          return self::_ErrorGenerator('[#153] ' . $ex->GetString());
        else
          return self::_ErrorGenerator('[#155] Неизвестная ошибка добавления');
      }
    } else
      return self::_ErrorGenerator("[#162] Инфоблок #" . $iblockIdTarget . " является торговым каталогом");

    return self::$Result;
  }

  static public function MountSKU(int $iblockIdRoot = 0, int $iblockIdTarget = 0): Result
  {
    global $APPLICATION;

    $iblockIdRoot = $iblockIdRoot ? $iblockIdRoot : self::$root_IBLOCK_ID;
    $iblockIdTarget = $iblockIdTarget ? $iblockIdTarget : self::$target_IBLOCK_ID;

    if (empty($iblockIdRoot)) return self::_ErrorGenerator('[#224] Не указан инфоблок для копирования');
    if (empty($iblockIdTarget)) return self::_ErrorGenerator('[#225] Не указан инфоблок для копирования');

    if (\CCatalog::GetByID($iblockIdTarget)) {
      $SKUPropId = self::_AddPropLinkSKU($iblockIdTarget, $iblockIdRoot);

      if (!$SKUPropId)
        return self::$Result;

      $res = \CCatalog::Update($iblockIdTarget, [
        'PRODUCT_IBLOCK_ID' => $iblockIdRoot,
        'SKU_PROPERTY_ID' => $SKUPropId
      ]);

      if (!$res) {
        if ($ex = $APPLICATION->GetException())
          return self::_ErrorGenerator('[#242] ' . $ex->GetString());
        else
          return self::_ErrorGenerator('[#242] Неизвестная ошибка добавления');
      }
    } else
      return self::_ErrorGenerator("[#162] Инфоблок #" . $iblockIdTarget . "не является торговым каталогом");

    return self::$Result;
  }
  #endregion

  #region Element
  private static ?string $SELF_XML = NULL;

  private static function  _CopyPic(int $picId): array
  {
    if (!empty($picId) && ($file = \CIBlock::makeFileArray($picId, false, null, array('allow_file_id' => true))))
      return (['COPY_FILE' => 'Y'] + $file);
  }

  static public function CheckChangeElement(int $iblockIdTarget, int $elementIdRoot): bool
  {
    $fn_ginger = function ($filter, $reg) use ($elementIdRoot){
      $x = ('\\Bitrix\\Catalog\\' . $reg[0])::getList([
        'select' => $reg[1],
        'filter' => $filter
      ]);

      $res = $x->fetchAll();
      $xq = md5(var_export($res, true));
      return $xq;
    };

    $fieldPrice = ['EXTRA_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY', 'QUANTITY_FROM', 'QUANTITY_TO'];
    $fieldProduct = ['AVAILABLE', 'VAT_ID', 'VAT_INCLUDED', 'QUANTITY', 'QUANTITY_RESERVED', 'QUANTITY_TRACE', 'CAN_BUY_ZERO', 'SUBSCRIBE', 'BUNDLE', 'PURCHASING_PRICE', 'PURCHASING_CURRENCY', 'WEIGHT', 'WIDTH', 'LENGTH', 'HEIGHT', 'MEASURE', 'BARCODE_MULTI', 'PRICE_TYPE', 'RECUR_SCHEME_TYPE', 'RECUR_SCHEME_LENGTH', 'TRIAL_PRICE_ID', 'WITHOUT_ORDER'];

    $rowRoot = \Bitrix\Iblock\ElementTable::getList([
      'select' => ['ID', 'XML_ID', 'ACTIVE_FROM'],
      'filter' => ['ID' =>  $elementIdRoot],
    ])->fetch();

    if ($rowTarget = self::CheckMountElement($iblockIdTarget, $rowRoot['XML_ID'])) {
      $hashRoot = ''
        . $fn_ginger(['PRODUCT_ID' =>  $elementIdRoot], ['PriceTable', $fieldPrice])
        . $fn_ginger(['ID' =>  $elementIdRoot], ['ProductTable', $fieldProduct]);
      $hashTarget = ''
        . $fn_ginger(['PRODUCT_ID' =>  $rowTarget['ID']], ['PriceTable', $fieldPrice])
        . $fn_ginger(['ID' =>  $rowTarget['ID']], ['ProductTable', $fieldProduct]);

      if ($hashRoot === $hashTarget)
        return false;
    }

    return true;
  }

  static function CheckMountElement(int $iblockIdTarget, string $elementXMLRoot): array
  {
    static $self_rowTargetMount = NULL;

    if (($self_rowTargetMount['ID'] ?? false) === $iblockIdTarget)
      return $self_rowTargetMount;

    $rowTargetMount = \Bitrix\Iblock\ElementTable::getList([
      'select' => ['ID', 'XML_ID', 'ACTIVE_FROM'],
      'filter' => ['IBLOCK_ID' => $iblockIdTarget, 'XML_ID' =>  '%#' . $elementXMLRoot],
      'limit' => 1
    ])->fetch();

    $self_rowTargetMount = $rowTargetMount ? $rowTargetMount : [];

    return $self_rowTargetMount;
  }

  static public function CopyElement(int $elementIdRoot, int $iblockIdTarget = 0, int $elementBitMain = 0b00): Result
  {
    $iblockIdTarget = $iblockIdTarget ? $iblockIdTarget : self::$target_IBLOCK_ID;

    if (empty($iblockIdTarget)) return self::_ErrorGenerator('[#p41] Не указан целевой инфоблок');
    if (empty($elementIdRoot)) return self::_ErrorGenerator('[#p42] Не указан элемент для копирования');

    $elementDataRoot = \CIBlockElement::GetByID($elementIdRoot)->Fetch();

    if (!$elementDataRoot)
      return self::_ErrorGenerator('[#p50] Ошибка получения данных');

    $elementDataTarget = $elementDataRoot;
    $syncDate = date('d.m.Y H:i:s');

    if ($elementBitMain & 0b11) {
      if ($elementBitMain & 0b10)
        self::$SELF_XML = $elementDataRoot['XML_ID'];
      $XML_ID = self::$SELF_XML . '#' . $elementDataRoot['XML_ID'];
    } else
      $XML_ID = GUIDv4();


    $elementDataTarget['CODE'] = md5($elementDataRoot['ID'] . $elementDataRoot['CODE'] . GUIDv4());
    $elementDataTarget['IBLOCK_ID'] = $iblockIdTarget;
    $elementDataTarget['IBLOCK_SECTION_ID'] = 0;
    $elementDataTarget['XML_ID'] = $elementDataTarget['EXTERNAL_ID'] = $XML_ID;

    unset(
      $elementDataTarget['ID'],
      $elementDataTarget['TMP_ID'],
      $elementDataTarget['WF_LAST_HISTORY_ID'],
      $elementDataTarget['PREVIEW_PICTURE'],
      $elementDataTarget['DETAIL_PICTURE']
    );

    $elementTarget = new \CIBlockElement();

    if ($elementIdTargetMount = (self::CheckMountElement($iblockIdTarget, $elementDataRoot['XML_ID'])['ID'] ?? 0)) {
      $elementTarget->Update($elementIdTargetMount, $elementDataTarget);
      $elementIdTarget = self::$last_ELEMENT_ID = $elementIdTargetMount;
    } else
      $elementIdTarget = self::$last_ELEMENT_ID = (int)$elementTarget->Add($elementDataTarget);


    if (!$elementIdTarget)
      return self::_ErrorGenerator('[#p78] Ошибка при копировании элемента: ' . $elementTarget->LAST_ERROR);

    foreach (['PREVIEW_PICTURE', 'DETAIL_PICTURE'] as $key) {
      if ($elementDataRoot[$key])
        $elementDataTargetUpdate[$key] = self::_CopyPic($elementDataRoot[$key]);
    }

    if ($elementDataTargetUpdate)
      $elementTarget->Update($elementIdTarget, $elementDataTargetUpdate);

    return self::$Result;
  }

  static private function _CanonocalValuePropLine($FL, &$linked)
  {
    $CDB = \CIBlockPropertyEnum::GetList([], $FL);

    while ($row = $CDB->Fetch())
      $linked[$row['XML_ID']] = $row['ID'];
  }

  static private function _RepackProp(array $prop, int $iblockIdTarget): array
  {
    $linked = [];
    $clean_prop = array_intersect_key($prop, [
      'VALUE' => null,
      'DESCRIPTION' => null,
      ...(isset($prop['VALUE_XML_ID'])
        ? ['VALUE_XML_ID' => null]
        : [])
    ]);

    if ($prop['MULTIPLE'] === 'N')
      foreach ($clean_prop as $k => $v)
        $clean_prop[$k] = [$v];

    if ($clean_prop['VALUE'] === false)
      return [$prop['CODE'] => [$clean_prop]];

    $PACK = array_map(function ($arr) use ($clean_prop, $prop, &$linked) {
      $el_PACK = array_combine(array_keys($clean_prop), $arr);

      switch ($prop['PROPERTY_TYPE']) {
        case 'F':
          if ($el_PACK['VALUE'])
            $el_PACK['VALUE'] = self::_CopyPic($el_PACK['VALUE']);
          break;
        case 'L':
          $linked[$el_PACK['VALUE_XML_ID']] = &$el_PACK['VALUE'];
          break;
      }

      unset($el_PACK['VALUE_XML_ID']);

      return $el_PACK;
    }, array_map(null, ...array_values($clean_prop)));

    $prop['PROPERTY_TYPE'] === 'L'
      && self::_CanonocalValuePropLine([
        "IBLOCK_ID" => $iblockIdTarget,
        "CODE" => $prop['CODE'],
        'XML_ID' => array_keys($linked)
      ], $linked);


    return [$prop['CODE'] => $PACK];
  }

  static private function _NeedProp(int $iblockIdTarget)
  {
    static $self_iblockIdTarget = 0;
    static $self_propertyNeed = [];

    if ($iblockIdTarget === $self_iblockIdTarget)
      return $self_propertyNeed;

    $propRes = \Bitrix\Iblock\PropertyTable::getList(array(
      'select' => array('CODE'),
      'filter' => array('IBLOCK_ID' => $iblockIdTarget),
      'order'  => array('SORT' => 'ASC')
    ));
    $propertyNeed = array_map(fn ($v) => $v['CODE'], $propRes->fetchAll());

    $self_propertyNeed = $propertyNeed;
    $self_iblockIdTarget = $iblockIdTarget;

    return $propertyNeed;
  }

  static public function CopyElementProperty(int $elementIdRoot, int $iblockIdRoot = 0, int $elementIdTarget = 0, int $iblockIdTarget = 0): Result
  {
    global $APPLICATION;

    $elementIdTarget = $elementIdTarget ? $elementIdTarget : self::$last_ELEMENT_ID;
    $iblockIdRoot = $iblockIdRoot ? $iblockIdRoot : self::$root_IBLOCK_ID;
    $iblockIdTarget = $iblockIdTarget ? $iblockIdTarget : self::$target_IBLOCK_ID;

    if (empty($elementIdRoot)) return self::_ErrorGenerator('[#p127] Не указан элемент для копирования');
    if (empty($iblockIdRoot)) return self::_ErrorGenerator('[#p124] Не указан инфоблок');
    if (empty($elementIdTarget)) return self::_ErrorGenerator('[#p126] Не указан целевой элемент');
    if (empty($iblockIdTarget)) return self::_ErrorGenerator('[#p125] Не указан целевой инфоблок');


    $list_elementPropsRoot = [$elementIdRoot => []];
    \CIBlockElement::GetPropertyValuesArray($list_elementPropsRoot, $iblockIdRoot, ['ID' => $elementIdRoot], ['CODE' => self::_NeedProp($iblockIdTarget)], ['GET_RAW_DATA' => 'Y']);

    $elementPropsRoot = $list_elementPropsRoot[$elementIdRoot];

    if (empty($elementPropsRoot)) return self::_ErrorGenerator('[#p135] Свойства элемента ' . $elementIdRoot . ' не найдены');

    $packedProp = [];
    foreach ($elementPropsRoot as $propRoot)
      $packedProp += self::_RepackProp($propRoot, $iblockIdTarget);

    \CIBlockElement::SetPropertyValuesEx($elementIdTarget, false, $packedProp, ['NewElement' => 'Y']);


    if ($ex = $APPLICATION->GetException())
      return self::_ErrorGenerator('[#p145] во время обновления ствойств произошла ошибка' . $ex->GetString());

    return self::$Result;
  }
  #endregion

  static public function addProductSKU(int $elementIdRoot, int $elementIdTarget)
  {
    (new \CIBlockElement)->Update($elementIdTarget, ["ACTIVE" => 'Y']);
    \CIBlockElement::SetPropertyValuesEx($elementIdTarget, false, [
      'CML2_LINK' => $elementIdRoot
    ], ['NewElement' => 'N']);
  }

  static public function CopyProductStore(int $elementIdRoot, int $elementIdTarget)
  {
    $field = ['STORE_ID', 'AMOUNT'];

    $CDB = \CCatalogStoreProduct::GetList([], ['PRODUCT_ID' => $elementIdRoot], false, false, $field);

    while ($row = $CDB->Fetch()) {
      \CCatalogStoreProduct::Add([
        'PRODUCT_ID' => $elementIdTarget,
        ...array_intersect_key($row, array_flip($field))
      ]);
    }
  }

  static public function CopyProductPrice(int $elementIdRoot, int $elementIdTarget)
  {
    $field = ['EXTRA_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY', 'QUANTITY_FROM', 'QUANTITY_TO'];

    $CDB = \CPrice::GetList([], ['PRODUCT_ID' => $elementIdRoot], false, false, $field);

    while ($row = $CDB->Fetch()) {
      \CPrice::Add([
        'PRODUCT_ID' => $elementIdTarget,
        ...array_intersect_key($row, array_flip($field))
      ], true);
    }
  }

  static public function CopyProductMain(int $elementIdRoot, int $elementIdTarget): Result
  {
    $field = ['AVAILABLE', 'VAT_ID', 'VAT_INCLUDED', 'QUANTITY', 'QUANTITY_RESERVED', 'QUANTITY_TRACE', 'CAN_BUY_ZERO', 'SUBSCRIBE', 'BUNDLE', 'PURCHASING_PRICE', 'PURCHASING_CURRENCY', 'WEIGHT', 'WIDTH', 'LENGTH', 'HEIGHT', 'MEASURE', 'BARCODE_MULTI', 'PRICE_TYPE', 'RECUR_SCHEME_TYPE', 'RECUR_SCHEME_LENGTH', 'TRIAL_PRICE_ID', 'WITHOUT_ORDER'];

    $CDB = \CCatalogProduct::GetList([], ['ID' => $elementIdRoot], false, false, $field);
    $row = $CDB->Fetch();


    $existProduct = \Bitrix\Catalog\Model\Product::getCacheItem($elementIdTarget, true);

    if ($existProduct) {
      $res = \Bitrix\Catalog\Model\Product::update(
        $elementIdTarget,
        [
          ...array_intersect_key($row, array_flip($field))
        ]
      );
    } else {
      $res = \Bitrix\Catalog\Model\Product::add([
        'ID' => $elementIdTarget,
        ...array_intersect_key($row, array_flip($field))
      ]);
    }

    return $res;
  }

  static public function DisableProduct(int $elementIdTarget)
  {
    (new \CIBlockElement)->Update($elementIdTarget, [
      'ACTIVE' => 'N'
    ]);
  }


  static public function FilterByCarolina($key)
  {
    $filter = [
      'TSVET' => true,
      'FORMA_RABOCHEY_CHASTI' => true,
      'DLINA_VYSOTA_RABOCHEY_CHASTI_MM' => true,
      'DIAMETR_RABOCHEY_CHASTI_MM' => true,
      'MAX_SKOROST_VRASHCHENIYA' => true,
      'ZERNISTOST' => true,
      'STRANA_PROIZVODSTVA' => true,
      'PAZ' => true,
      'UGLUBLENIE_NA_DUGE' => true,
      'DLINA_KHVOSTOVIKA' => true,
      'OBYEM_STERILIZATSIONNOY_KAMERY' => true,
      'SPETSIFICHESKOE_OBOZNACHENIE_ELASTIKA' => true,
      'KONUSNOST' => true,
      'PROPIS' => true,
      'TIP' => true,
      'TSVETOVAYA_MARKIROVKA' => true,
      'NALICHIE_KRYUCHKA' => true,
      'GARANTIYNYY_SROK' => true,
      'VIDY_NASECHEK_FREZ' => true,
      'VARIANT_ISPOLNENIYA_' => true,
      'RAZMER_DUGI' => true,
      'SHIRINA' => true,
      'RAZMER' => true,
      'DLINA' => true,
      'NOMER_ZUBA' => true,
      'PROIZVODITELNOST' => true,
      'TVERDOST' => true,
      'SECHENIE' => true,
      'MAKSIMALNAYA_OBLAST_ISSLEDOVANIYA' => true,
      'SHIRINA_SHVA' => true,
      'TIP_ASPIRATSII' => true,
      'OBEM' => true,
      'DIAMETR' => true,
      'OBSHCHAYA_DLINA_BORA_MM' => true,
      'CHELYUST' => true,
      'OBYEM' => true,
      'SROK_GODNOSTI' => true,
      'NAZNACHENIE' => true,
      'KLASS_STERILIZATSII' => true,
      'DIAMETR_KHVOSTOVIKA_MM' => true,
      'OBEM_REZERVUARA' => true,
      'VKUS' => true,
      'USLOVNYY_RAZMER_USP' => true,
      'OBEM_VANNY' => true,
      'FASOVKA' => true,
      'VARIANT_TORKA' => true,
      'MYAGKOST' => true,
      'FORMA_DUGI' => true,
      'PODACHA_INSTRUMENTOV' => true,
      'TOLSHCHINA' => true,
      'TIP_UPRAVLENIYA' => true,
      'FORMA' => true,
      'GEOMETRIYA_LEZVIY' => true,
      'SILA_VOZDEYSTVIYA' => true,
      'FASON' => true,
      'TIP_IGLY' => true,
      'KPI' => true,
      'TOLSHCHINA_1' => true,
    ];

    return ($filter[$key] ?? false);
  }

}
