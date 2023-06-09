<?php

namespace MIT\Model\FavoriteProduct\Method;

use MIT\Model\FavoriteProduct\IStorage;
use MIT\Model\FavoriteProduct\Storage;

use MIT\Model\FavoriteProduct;
use MIT\Model\FavoriteProduct\Exception\FPException;

class HLBlock extends Storage implements IStorage
{
  const HBLOCK_ID = 4;
  private static $EntityHlBlock;

  static public function Init(): IStorage
  {
    return parent::Init();
  }

  protected function __construct()
  {
    static::EntityHL();
  }

  private static function EntityHL()
  {
    if (empty(static::$EntityHlBlock)) {
      $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getById(static::HBLOCK_ID)->fetch();
      $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
      static::$EntityHlBlock = $obEntity->getDataClass();
    }

    if (empty(static::$EntityHlBlock))
      throw new FPException("Fail get entityHL", 1);

    return static::$EntityHlBlock;
  }

  public static function CheckEntityHL(): bool
  {
    return empty(static::EntityHL());
  }

  public function loadList(): array
  {
    $this->loadState(true);

    $this->product_list = [];
    $userFilter = ['UF_FVR_USER_ID' => FavoriteProduct::ActiveUserID()];

    $resData = self::EntityHL()::getList([
      'select' => ['ID', 'UF_FVR_PRODUCT_ID'],
      'filter' => $userFilter,
      'order'  => ['ID' => 'DESC'],
    ]);

    while ($arItem = $resData->Fetch())
      $this->product_list[(int)$arItem['ID']] = (int)$arItem['UF_FVR_PRODUCT_ID'];

    return $this->product_list;
  }

  protected function _add(int $id_product): bool
  {
    return $this->_addList([$id_product]);
  }

  protected function _addList(array $add_list): bool
  {
    $res = true;
    $product_list = array_unique($add_list);

    foreach ($product_list as $product_id) {
      $result = static::EntityHL()::add(array(
        'UF_FVR_USER_ID' =>  FavoriteProduct::ActiveUserID(),
        'UF_FVR_PRODUCT_ID' => $product_id,
      ));
      $res &= (bool)$result->isSuccess();
    }

    $this->_set();

    return $res;
  }

  protected function _delete(int $id_product): bool
  {
    return $this->_deleteList([$id_product]);
  }

  protected function _deleteList(array $exclude_list): bool
  {
    $res = true;
    $flip_product_list = array_flip($this->getList());

    foreach ($exclude_list as $product_id) {
      $result = static::EntityHL()::delete($flip_product_list[$product_id]);
      $res &= (bool)$result->isSuccess();
    }

    $this->_set();

    return $res;
  }

  protected function _set(array $product_list = []): bool
  {
    $this->loadState(false);
    $this->loadList();
    return $this->loadState();
  }
}
