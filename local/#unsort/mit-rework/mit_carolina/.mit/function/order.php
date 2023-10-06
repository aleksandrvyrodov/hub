<?php

namespace MIT\Function\Order;

function ChangedProductIdInOrder(\Bitrix\Main\Event $event): \Bitrix\Main\EventResult
{
  $Order = $event->getParameter("ENTITY");

  $fn_XmlRev = fn ($v) => array_reverse(explode('#', $v))[0];
  $Basket = $Order->getBasket();

  foreach ($Basket->getBasketItems() as $BasketItem) {
    //region (S-1)
    $BitrixSaleInternalsFields = (new \ReflectionClass($BasketItem))
      ->getProperty('fields')
      ->getValue($BasketItem);

    $RP_values = (new \ReflectionClass($BitrixSaleInternalsFields))
      ->getProperty('values');

    $values = $RP_values->getValue($BitrixSaleInternalsFields);
    $values['PRODUCT_XML_ID'] = $fn_XmlRev($values['PRODUCT_XML_ID']);
    $RP_values->setValue($BitrixSaleInternalsFields, $values);
    //endregion

    //region (S-2|3)
    $BitrixSaleBasket = (new \ReflectionClass($BasketItem))
      ->getProperty('collection')
      ->getValue($BasketItem);
    $BitrixSaleOrder = (new \ReflectionClass($BitrixSaleBasket))
      ->getProperty('order')
      ->getValue($BitrixSaleBasket);
    $BitrixSaleDiscount = (new \ReflectionClass($BitrixSaleOrder))
      ->getProperty('discount')
      ->getValue($BitrixSaleOrder);

    if ($BitrixSaleDiscount) {
      $RP_orderData = (new \ReflectionClass($BitrixSaleDiscount))
        ->getProperty('orderData');


      $orderData = $RP_orderData->getValue($BitrixSaleDiscount);

      $orderData['BASKET_ITEMS'][$values['ID']]['PRODUCT_XML_ID'] = $fn_XmlRev($orderData['BASKET_ITEMS'][$values['ID']]['PRODUCT_XML_ID']);
      $orderData['BASKET_ITEMS'][$values['ID']]['PROPERTIES']['PRODUCT.XML_ID']['VALUE'] = $fn_XmlRev($orderData['BASKET_ITEMS'][$values['ID']]['PROPERTIES']['PRODUCT.XML_ID']['VALUE']);

      $RP_orderData->setValue($BitrixSaleDiscount, $orderData);
    }
    //endregion

    //region (S-4)
    $BitrixSaleBasketPropertiesCollection = (new \ReflectionClass($BasketItem))
      ->getProperty('propertyCollection')
      ->getValue($BasketItem);

    if ($BitrixSaleBasketPropertiesCollection) {
      $collection = (new \ReflectionClass($BitrixSaleBasketPropertiesCollection))
        ->getProperty('collection')
        ->getValue($BitrixSaleBasketPropertiesCollection);

      foreach ($collection as $BitrixSaleBasketPropertyItem) {
        $BitrixSaleInternalsFields = (new \ReflectionClass($BitrixSaleBasketPropertyItem))
          ->getProperty('fields')
          ->getValue($BitrixSaleBasketPropertyItem);

        $RP_values = (new \ReflectionClass($BitrixSaleInternalsFields))
          ->getProperty('values');

        $values = $RP_values->getValue($BitrixSaleInternalsFields);
        if ($values['CODE'] != 'PRODUCT.XML_ID')
          continue;

        $values['VALUE'] = $fn_XmlRev($values['VALUE']);
        $RP_values->setValue($BitrixSaleInternalsFields, $values);

        break;
      }
    }
    //endregion


    $BasketItem->setField('PRODUCT_XML_ID',
      array_reverse(explode('#', $BasketItem->getField('PRODUCT_XML_ID')))[0]
    );
  }


  return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS);
}


function ChangedProductIdInBasket(\Bitrix\Main\Event $event): \Bitrix\Main\EventResult
{
  $Basket = $event->getParameter("ENTITY");

  $fn_XmlRev = fn ($v) => array_reverse(explode('#', $v))[0];


  foreach ($Basket->getBasketItems() as $BasketItem) {
    //region (S-1)
    $BitrixSaleInternalsFields = (new \ReflectionClass($BasketItem))
      ->getProperty('fields')
      ->getValue($BasketItem);

    $RP_values = (new \ReflectionClass($BitrixSaleInternalsFields))
      ->getProperty('values');

    $values = $RP_values->getValue($BitrixSaleInternalsFields);
    $values['PRODUCT_XML_ID'] = $fn_XmlRev($values['PRODUCT_XML_ID']);
    $RP_values->setValue($BitrixSaleInternalsFields, $values);
    //endregion



    //region (S-4)
    $BitrixSaleBasketPropertiesCollection = (new \ReflectionClass($BasketItem))
      ->getProperty('propertyCollection')
      ->getValue($BasketItem);

    if ($BitrixSaleBasketPropertiesCollection) {
      $collection = (new \ReflectionClass($BitrixSaleBasketPropertiesCollection))
        ->getProperty('collection')
        ->getValue($BitrixSaleBasketPropertiesCollection);

      foreach ($collection as $BitrixSaleBasketPropertyItem) {
        $BitrixSaleInternalsFields = (new \ReflectionClass($BitrixSaleBasketPropertyItem))
          ->getProperty('fields')
          ->getValue($BitrixSaleBasketPropertyItem);

        $RP_values = (new \ReflectionClass($BitrixSaleInternalsFields))
          ->getProperty('values');

        $values = $RP_values->getValue($BitrixSaleInternalsFields);
        if ($values['CODE'] != 'PRODUCT.XML_ID')
          continue;

        $values['VALUE'] = $fn_XmlRev($values['VALUE']);
        $RP_values->setValue($BitrixSaleInternalsFields, $values);

        break;
      }
    }
    //endregion


    $BasketItem->setField('PRODUCT_XML_ID',
      array_reverse(explode('#', $BasketItem->getField('PRODUCT_XML_ID')))[0]
    );
  }


  return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::SUCCESS);
}