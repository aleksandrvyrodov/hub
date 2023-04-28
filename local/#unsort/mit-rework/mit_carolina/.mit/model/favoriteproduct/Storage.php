<?php

namespace MIT\Model\FavoriteProduct;

use MIT\Model\FavoriteProduct;
use MIT\Model\FavoriteProduct\Exception\FPException;

abstract class Storage
{
  const SELF_LOAD = true;

  protected array $product_list = [];
  protected bool  $load = false;
  static private array $listStorage = [];

  abstract public function loadList();
  abstract protected function _add(int $id_product);
  abstract protected function _addList(array $list);
  abstract protected function _delete(int $id_product);
  abstract protected function _deleteList(array $exclude_list);
  abstract protected function _set(array $id_product);

  static protected function Init(): IStorage
  {
    if (empty(self::$listStorage[static::class]))
      self::$listStorage[static::class] = new static();

    return self::$listStorage[static::class];
  }

  public function getList(bool $self_load = self::SELF_LOAD): array
  {
    if ($this->loadState() && $self_load)
      return $this->product_list;

    $product_list = $this->loadList();

    if ($this->excludeDisableProduct($product_list))
      return $this->product_list;
    else
      return $product_list;
  }

  public function add(int $id_product): bool
  {
    if (!self::checkProduct($id_product))
      return false;

    if ($this->issetProduct($id_product))
      return true;

    return $this->_add($id_product);
  }

  public function addList(array $list_id_product): bool
  {
    foreach ($list_id_product as $key => $id_product)
      if (
        !self::checkProduct($id_product)
        || $this->issetProduct($id_product)
      ) unset($list_id_product[$key]);


    return $this->_addList($list_id_product);
  }

  public function delete(int $id_product): bool
  {
    if (!$this->issetProduct($id_product))
      return true;

    return $this->_delete($id_product);
  }

  public function deleteList(array $list_id_product): bool
  {
    foreach ($list_id_product as $key => $id_product)
      if (!$this->issetProduct($id_product))
        unset($list_id_product[$key]);


    return $this->_deleteList($list_id_product);
  }

  protected function loadState(?bool $load = null): bool
  {
    if (!is_null($load))
      $this->load = $load;

    return $this->load;
  }

  public function issetProduct(int $product_id): bool
  {
    return in_array($product_id, $this->getList());
  }

  public static function checkProduct(int $product_id): bool
  {
    return FavoriteProduct::checkProduct($product_id);
  }

  protected function excludeDisableProduct(array $list_product_ids): bool|int
  {
    $exclude = FavoriteProduct::detectDisableProduct($list_product_ids);

    if (!empty($exclude)) {
      if ($this->_deleteList($exclude))
        return true;
      else
        throw new FPException("Fail exclude product in favorite list", 1);
    } else
      return false;
  }
}
