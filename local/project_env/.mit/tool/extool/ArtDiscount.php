<?php

namespace MIT\Tool;

class ArtDiscount
{

  static private $pId = false;

  static public function OnGetDiscount($intProductID, $intIBlockID, $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, $boolSKU, $boolGetIDS)
  {

    self::$pId = $intProductID;

    return true;
  }

  static public function OnGetDiscountResult(&$arResult)
  {

    $intProductID = self::$pId;

    if ($intProductID) {
      $newAr = array();
      foreach ($arResult as $key => $val) {
        if (strpos($val["NAME"], "#KMP#") === false || isset($_SESSION['KMP'][$intProductID]) || $_SESSION['KMPBASKET'][$intProductID]) {
          $newAr[] = $val;
        }
      }
      $arResult = $newAr;
    }
  }
}
