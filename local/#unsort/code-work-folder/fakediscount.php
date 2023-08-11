<?php

/**
 * @version 23.300.200
 */

# bitrix/modules/sale/lib/discountbase.php:5541
//(include('/home/bitrix/ext_www/carolinashop.ru/.mit/static/fakediscount.php'))($currentList);

const MIT_FAKE_DISCOUNT = 50;
const MIT_FAKE_DISCOUNT_ID = 1;

return function(&$currentList)
{
  $MIT_PRICE = 40;
  //                                ['APLICATION']
  $currentList[MIT_FAKE_DISCOUNT_ID]['APPLICATION'] = <<<T
        function (&\$arOrder){
        \Bitrix\Sale\Discount\Actions::applyToBasket(\$arOrder, array (
          'VALUE' => -$MIT_PRICE,
          'UNIT' => 'P',
          'LIMIT_VALUE' => 0,
        ), "");};
        T;
  $currentList[MIT_FAKE_DISCOUNT_ID]['SHORT_DESCRIPTION'] = serialize((function ($_) use ($MIT_PRICE) {
    $_["VALUE"] = $MIT_PRICE;
    return $_;
  })(unserialize($currentList[MIT_FAKE_DISCOUNT_ID]['SHORT_DESCRIPTION'])));
  $currentList[MIT_FAKE_DISCOUNT_ID]['SHORT_DESCRIPTION_STRUCTURE']['VALUE'] = $MIT_PRICE;
  $currentList[MIT_FAKE_DISCOUNT_ID]['ACTIONS']['CHILDREN'][0]['DATA']['Value'] = $MIT_PRICE;
};
