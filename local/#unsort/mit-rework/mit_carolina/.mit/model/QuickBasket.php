<?php

namespace MIT\Model;

use MIT\Loader;

class QuickBasket implements IIncludeDependencies, ISingleton
{
  private array $raw_rows = [];
  private static QuickBasket $QuickBasket;

  public static function Dep(Loader $Loader): bool
  {
    // try {
    $res = 1
      && $Loader->loadModule('sale')
      && $Loader->loadModule('catalog')
      && $Loader->loadModule('iblock');

    // } catch (LoaderException $le) {
    # LOG: failed to load dependencies
    // } finally{
    //   return $res;
    // }
    return $res;
  }

  public static function Init(): QuickBasket
  {
    if (empty(self::$QuickBasket))
      self::$QuickBasket = new self();

    return self::$QuickBasket;
  }



  private function __construct()
  {
  }

  public function Run(): int
  {

    return 0;
  }


  public static function ParseXlsx(string $file_path): array|false
  {
    if ($xlsx = \Shuchkin\SimpleXLSX::parse($_FILES['file']['tmp_name'])) {
      $rows = $xlsx->rows();
      array_shift($rows);

      if (count($rows))
        return $rows;
      else
        return false;
    }

    return false;
  }

  public static function SorterRawRows(array $rows): array
  {
    $arXlsOrder = [
      'XML' => [],
      'ARTICLE' => [],
    ];

    foreach ($rows as $row) {
      $article = htmlspecialchars((string)$row[0] ?? '');
      $quantity = (float)$row[1] ?? 0; // QUANTITY
      $xmlId = htmlspecialchars((string)$row[3] ?? ''); // XML_ID

      if (!($quantity > 0 && (mb_strlen($xmlId) > 0 || mb_strlen($article) > 0)))
        continue;

      if (!mb_strlen($xmlId))
        $arXlsOrder['ARTICLE'][$article] = $quantity;
      else
        $arXlsOrder['XML'][$xmlId] = $quantity;


      unset($xmlId, $article, $quantity);
    }

    return $arXlsOrder;
  }

  static public function ConvertArticleToXmlId(array &$arXlsOrder)
  {
    foreach (self::_GetXmlIdByArticle(array_keys($arXlsOrder['ARTICLE'])) as $item)
      $arXlsOrder['XML'][$item['IBLOCK_ELEMENT_PROPERTY_element_XML_ID']] ??= $arXlsOrder['ARTICLE'][$item['VALUE']];
    unset($arXlsOrder['ARTICLE']);
  }

  static public function GetStructuredRows(array $arXlsOrder): array
  {
    $list_El = array_filter(
      self::_GetElementPreByXmlId(array_keys($arXlsOrder['XML'])),
      fn ($El) => $El['ACTIVE'] === 'Y'
    );

    return self::_MakeStructureRows(array_map(fn ($El) => $El['ID'], $list_El), $arXlsOrder['XML']);
  }

  private static function _GetXmlIdByArticle($list_articles): array
  {
    $PROP_ID = 380;
    $PROP_CODE = 'CML2_ARTICLE';

    $q = new \Bitrix\Main\Entity\Query(\Bitrix\Iblock\ElementPropertyTable::getEntity());
    $q
      /* ->registerRuntimeField(
        'property',
        array(
          'data_type' => \Bitrix\Iblock\PropertyTable::class,
          'reference' => [
            '=this.IBLOCK_PROPERTY_ID' => 'ref.ID',
            'ref.IBLOCK_ID' => new \Bitrix\Main\DB\SqlExpression('?', \MIT\Catalog\MAIN_CATALOG_ID),
            'ref.CODE' => new \Bitrix\Main\DB\SqlExpression('?', $PROP_CODE),
          ],
          'join_type' => 'INNER'
        )
      ) */

      ->registerRuntimeField(
        'element',
        array(
          'data_type' => \Bitrix\Iblock\ElementTable::class,
          'reference' => [
            '=this.IBLOCK_ELEMENT_ID' => 'ref.ID',
            'ref.IBLOCK_ID' => new \Bitrix\Main\DB\SqlExpression('?', \MIT\Catalog\MAIN_CATALOG_ID),
          ],
          'join_type' => 'INNER'
        )
      )

      ->setSelect(['element.XML_ID', 'VALUE'])
      ->setFilter([
        'VALUE' => $list_articles,
        'IBLOCK_PROPERTY_ID' => $PROP_ID
      ])
      #
    ;

    $Result = $q->exec();

    return $Result->fetchAll();
  }

  private static function _GetElementPreByXmlId($list_xmlId): array
  {

    $list_Result = [];
    $selectEl = ['IBLOCK_ID', 'NAME', 'ACTIVE', 'ID', 'XML_ID'];

    $iblockIdSKU = \Bitrix\Catalog\CatalogIblockTable::getList([
      'filter' => [
        'PRODUCT_IBLOCK_ID' => \MIT\Catalog\MAIN_CATALOG_ID,
      ],
      'select' => ['IBLOCK_ID'],
      'limit' => 1
    ])->fetch()['IBLOCK_ID'] ?? false;

    $list_El = \Bitrix\Iblock\ElementTable::getList([
      'select' => $selectEl,
      'filter' => ['IBLOCK_ID' => \MIT\Catalog\MAIN_CATALOG_ID, 'XML_ID' =>  $list_xmlId],
    ])->fetchAll();

    while ($El = array_shift($list_El))
      $list_Result[$El['XML_ID']] = $El;

    if ($iblockIdSKU) {
      $list_El = \Bitrix\Iblock\ElementTable::getList([
        'select' => $selectEl,
        // 'filter' => ['IBLOCK_ID' => $iblockIdSKU, 'XML_ID' => array_map(fn ($xmlId) => '%#' . $xmlId,  $list_xmlId)],
        'filter' => ['IBLOCK_ID' => $iblockIdSKU, '%XML_ID' => $list_xmlId],
      ])->fetchAll();

      while ($El = array_shift($list_El))
        $list_Result[explode('#', $El['XML_ID'])[1]] = $El;
    }

    return $list_Result;
  }

  private static function _MakeStructureRows($list_id, $list_quantity): array
  {
    $list_Result = [];

    $q = new \Bitrix\Main\Entity\Query(\Bitrix\Catalog\ProductTable::getEntity());
    $q
      ->registerRuntimeField(
        'element',
        array(
          'data_type' => \Bitrix\Iblock\ElementTable::class,
          'reference' => [
            '=this.ID' => 'ref.ID',
          ],
          'join_type' => 'INNER'
        )
      )

      ->setSelect(['element.ID', 'element.NAME', 'element.XML_ID', 'QUANTITY'])
      ->setFilter([
        'ID' => $list_id,
        'AVAILABLE' => 'Y'
      ])
      #
    ;

    $Result = $q->exec();

    $list_El = $Result->fetchAll();

    while ($El = array_shift($list_El))
      $list_Result[($key = array_reverse(explode('#', $El['CATALOG_PRODUCT_element_XML_ID']))[0])] = [
        'QUANTITY' => (int)$El['QUANTITY'],
        'NEED' => $list_quantity[$key],
        'ID' => (int)$El['CATALOG_PRODUCT_element_ID'],
        'NAME' => (string)$El['CATALOG_PRODUCT_element_NAME'],
      ];



    return $list_Result;
  }


  public static function ClearBasket(): void
  {
    $res = \CSaleBasket::GetList(
      array(),
      array(
        'FUSER_ID' => \CSaleBasket::GetBasketUserID(),
        'LID'      => SITE_ID,
        'ORDER_ID' => 'null',
        'DELAY'    => 'N',
        'CAN_BUY'  => 'Y',
      )
    );
    while ($row = $res->fetch())
      \CSaleBasket::Delete($row['ID']);
  }

  public static function  AddBasket(array $s_rows): array
  {
    $Result = [
      'ADDED' => [],
      'ERRORS' => []
    ];

    foreach ($s_rows as $item) {
      $oBasketAddResult = \Bitrix\Catalog\Product\Basket::addProduct([
        'PRODUCT_ID' => $item['ID'],
        'QUANTITY'   => ($quantity = min($item['NEED'], $item['QUANTITY'])),
      ]);

      if ($oBasketAddResult->isSuccess())
        $Result['ADDED'][] = $item['NAME'] . " - ($quantity)";
      else
        $Result['ERRORS'][] = [sprintf("Товар id:%s", $item['ID']), $oBasketAddResult->getErrorMessages()];
    }

    return $Result;
  }
}
