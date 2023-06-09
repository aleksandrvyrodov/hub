<?php

namespace MIT\Model;

use MIT\Loader;
use MIT\Model\FavoriteProduct\Method\Cookie;
use MIT\Model\FavoriteProduct\Exception\FPException;
use MIT\Model\FavoriteProduct\Method\HLBlock;
use MIT\Model\FavoriteProduct\IStorage;

class FavoriteProduct implements IIncludeDependencies, ISingleton
{
  const LOCK_MODE = 0b100;
  const MODE_COOKIE = 0b001;
  const MODE_USER = 0b010;

  private $mode;
  private static int $UserID = 0;

  private static FavoriteProduct $FavoriteProduct;
  public static IStorage $Storage;

  public static function Dep(Loader $Loader): bool
  {
    // try {
    $res = 1
      && $Loader->loadModule('highloadblock')
      && $Loader->loadModule('iblock');

    // } catch (LoaderException $le) {
    # LOG: failed to load dependencies
    // } finally{
    //   return $res;
    // }
    return $res;
  }

  public static function Init(): FavoriteProduct
  {
    if (empty(self::$FavoriteProduct))
      self::$FavoriteProduct = new self();

    return self::$FavoriteProduct;
  }

  public static function ActiveUserID($forse = false)
  {
    if ($forse || empty(self::$UserID)) {
      global $USER;
      self::$UserID = (int)$USER->GetID();
    }

    return self::$UserID;
  }

  private function __construct()
  {
    if (self::ActiveUserID()) {
      $this->stateMode(self::MODE_USER);

      if (HLBlock::CheckEntityHL())
        $this->stateMode(self::MODE_COOKIE | self::LOCK_MODE);
    } else
      $this->stateMode(self::MODE_COOKIE);
  }

  private function stateMode(int $mode = 0, $mount = true): int
  {
    if ($mode === 0)
      return $this->mode;

    $mode = $mode & (self::MODE_COOKIE | self::MODE_USER | self::LOCK_MODE);

    if (!$mode)
      $mode = self::MODE_COOKIE | self::LOCK_MODE;

    if ($mount)
      $this->mode = $mode;

    return $mode;
  }

  public function &initStorage(int $mode = 0): IStorage
  {
    $mode = $this->stateMode($mode, false);

    switch (true) {
      case ($mode & self::MODE_COOKIE):
        self::$Storage = Cookie::Init();
        break;
      case ($mode & self::MODE_USER):
        try {
          self::$Storage = HLBlock::Init();
          break;
        } catch (FPException) {
          1;
        }
      default:
        $this->stateMode(self::MODE_COOKIE | self::LOCK_MODE);
        self::$Storage = Cookie::Init();
    }

    return self::$Storage;
  }

  public function switchStorage(int $mode): void
  {
    $this->initStorage($mode);
  }

  public static function checkProduct(int $product_id): bool
  {
    return empty(self::detectDisableProduct([$product_id]));
  }

  public static function detectDisableProduct(array $list_product_ids): array
  {
    $stack = [];
    $arSelect = array("ID");
    $arFilter = array("ID" => $list_product_ids, "ACTIVE" => "Y");

    $res = \CIblockElement::GetList([], $arFilter, false, false, $arSelect);

    while ($ar_res = $res->GetNext())
      $stack[] = (int)$ar_res['ID'];

    return array_diff($list_product_ids, $stack);
  }

  private static function ExchargeInit(&$Cookie, &$HLBlock): bool
  {
    try {
      $Cookie = Cookie::Init();
      $Cookie->exchargeList = $Cookie->getList();

      $HLBlock = HLBlock::Init();
      $HLBlock->exchargeList = $HLBlock->getList();

      return true;
    } catch (FPException) {
      return false;
    }
  }

  public function ExchargeHlToCookie(): bool
  {
    if (!($res = self::ExchargeInit($Cookie, $HLBlock)))
      return $res;

    $Cookie->deleteList($Cookie->exchargeList);
    $this->switchStorage(self::MODE_COOKIE);

    return $Cookie->addList(array_values($HLBlock->exchargeList));
  }

  public function ExchargeCookieToHl(): bool
  {
    if (!($res = self::ExchargeInit($Cookie, $HLBlock)))
      return $res;

    $this->switchStorage(self::MODE_USER);



    return $HLBlock->addList(array_values($Cookie->exchargeList));
  }
}
