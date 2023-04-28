<?php

namespace MIT\Model\FavoriteProduct\Method;

use MIT\Model\FavoriteProduct\IStorage;
use MIT\Model\FavoriteProduct\Storage;

class Cookie extends Storage implements IStorage
{
  const COOKIE_NAME = 'FavoriteProduct';

  private static array $CookieOpt;

  static public function Init(): IStorage
  {
    return parent::Init();
  }

  protected function __construct()
  {
    if (empty(static::$CookieOpt))
      static::$CookieOpt = [
        'expires' => time() + 60 * 60 * 24 * 365,
        'path' => '/',
        // 'domain' => $_SERVER['SERVER_NAME'],
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict' // None || Lax  || Strict
      ];
  }

  public function loadList(): array
  {
    $this->loadState(true);
    $this->product_list = (array)json_decode($_COOKIE[self::COOKIE_NAME] ?? '[]');
    return $this->product_list;
  }

  protected function _add(int $id_product): bool
  {
    return $this->_addList([$id_product]);
  }

  protected function _addList(array $add_list): bool
  {
    $product_list = array_unique(array_merge($this->getList(), $add_list));

    return $this->_set($product_list);
  }

  protected function _delete(int $id_product): bool
  {
    return $this->_deleteList([$id_product]);
  }

  protected function _deleteList(array $exclude_list): bool
  {
    $product_list = array_values(array_diff($this->getList(), $exclude_list));

    return $this->_set($product_list);
  }

  protected function _set(array $product_list): bool
  {
    $this->loadState(false);

    $state = (bool)count($product_list);
    $list = $state ? json_encode($product_list) : '[]';

    if ($res = setcookie(
      self::COOKIE_NAME,
      $list,
      [
        'expires' => $state ? static::$CookieOpt['expires'] : time() - 1,
      ] + static::$CookieOpt
    )) {
      $_COOKIE[self::COOKIE_NAME] = $list;
      $this->loadList();
    }

    return (bool)$res;
  }
}
