<?php

#region Include
use MIT\Model\CarolinaPropertyFilter;
use MIT\Model\ParamCopyIBlock;
use MIT\Model\WorkshopIBlock;
use MIT\Model\WorkshopIBlockCatalog;
use MIT\Model\WorkshopIBlockElementCollector;
use MIT\Model\WorkshopIBlockElement;
use MIT\Model\WorkshopIBlockProduct;
use MIT\Model\WorkshopUnity;
use MIT\Tool\Logger;

use const MIT\Shell\EX1C_ELEMENT_MAX_TSEC;

# \--------------------------------------------------
if (php_sapi_name() !== 'cli') {
  echo 'This script use only cli';
  exit();
}
# /--------------------------------------------------

# \--------------------------------------------------
require_once __DIR__ . '/../../bitrix/modules/main/include/prolog_before.php';
# /--------------------------------------------------
#endregion

!defined("MIT\\Shell\\LAST_MOD_TIME")
  && define("MIT\\Shell\\LAST_MOD_TIME", 1);

#region Workshop
$start = microtime(true);

$iblockIdRoot = \MIT\Catalog\MAIN_CATALOG_ID;
$COUNTER = 0;
$COUNTER_J = 0;

try {
  $Log = new Logger('upgrage-catalog.log', true);
  $WIB = new WorkshopIBlock($iblockIdRoot);
  $WIBC = new WorkshopIBlockCatalog($WIB);

  WorkshopUnity::mountLogObject($Log);

  $WIB->Log("START ----------------------------------------", 0, $start);

  $catalogMount = $WIBC
    ->Log("Проверка инфоблока для SKU")
    ->mountedByWIB();

  !$catalogMount
    && $WIB
    ->Log('Копирование исходного инфоблока')
    ->checkedChain($WIB->CopyIBlock(new ParamCopyIBlock(
      iblockTypeTarget: 'aspro_max_catalog',
      suffixName: ' (SKU)',
      suffixXmlId: '#',
    )));

  $WIB
    ->Log('Копирование свойств исходного инфоблока')
    ->checkedChain($WIB->CopyIBlockProperty(new CarolinaPropertyFilter));

  !$catalogMount
    && $WIBC
    ->setProductIdByWIB()
    ->Log('Монтирование каталога')
    ->checkedChain($WIBC->MountCatalog())
    ->Log('Монтирование SKU')
    ->checkedChain($WIBC->MountSKU())
    #
  ;

  $WIB->Log('--/\------------------------------------------');

  $WIBEC = new WorkshopIBlockElementCollector($WIB);
  [$elementProduct, $elementSKU] = $WIBEC->CollectorDataInfo();
  $WIB->Log("SCOPE OF WORK [$elementProduct|$elementSKU]");

  foreach ($WIBEC->CollectorData() as $CollectorDataElement) {
    $FullDataElementMain = null;

    foreach ($WIBEC->FullData($CollectorDataElement->NAME) as $FullDataElement) {
      $LoopTime = microtime(true);
      $WIB->Log('START ELEMENT --\/----------------------------');

      try {
        $WIBE = new WorkshopIBlockElement($WIB, $FullDataElement);
        $WIBE
          ->Log("#[$COUNTER|" . $COUNTER_J++ . ']')
          ->Log("Получен элемент ([" . $FullDataElement->ID . '] ' . $FullDataElement->NAME . ')', 1)
          ->Log('Проверка элемента >>', 1)
          ->CheckChangeElement(MIT\Shell\LAST_MOD_TIME, (bool) $FullDataElementMain)
          ->Log('<< ----', 1)
          ->Log('Копирование элемента >>', 1)
          ->checkedChain($WIBE->CopyElement($FullDataElementMain, true))
          ->Log('<< ----', 1)
          ->Log('Копирование свойств >>', 1)
          ->checkedChain($WIBE->CopyElementProperty(), false)
          ->Log('<< ----', 1)
          #
        ;

        $WIBP = new WorkshopIBlockProduct($WIBE);
        $WIBP
          ->Log('Копирование информации о складах продукта >>', 2)
          ->checkedChain($WIBP->CopyProductStore(), false)
          ->Log('<< ----', 2)
          ->Log('Копирование информации о прайсах продукта >>', 2)
          ->checkedChain($WIBP->CopyProductPrice(), false)
          ->Log('<< ----', 2)
          ->Log('Копирование основной информации продукта >>', 2)
          ->checkedChain($WIBP->CopyProductMain())
          ->Log('<< ----', 2)
          #
        ;

        $WIBE
          ->Log('Монтирование SKU >>', 2)
          ->checkedChain($WIBE->MountProductSKU())
          ->Log('<< ----', 3)
          #
        ;

        $WIBE
          ->Log('Активация SKU >>', 3)
          ->checkedChain($WIBE->Enable($WIBE->getFullDataElementTarget()->ID))
          ->Log('<< ----', 3)
          #
        ;

        $COUNTER++;
      } catch (\Throwable $th) {
        if (($SkipT = ($th->getCode() === -2)) || $th->getCode() === -3)
          $WIB->Log('SKIP > ' . $th->getMessage(), 1);
        else
          $WIB->Log('ERROR > ' . $th->getMessage(), 1);
      } finally {
        if ($FullDataElementMain)
          $SkipT
            && $WIBE
            ->Log('Деактивация продукта >> ', 3)
            ->checkedChain($WIBE->Disable($WIBE->getFullDataElementRoot()->ID))
            ->Log('<< ----', 3)
            #
          ;
        else {
          $FullDataElementMain = $FullDataElement;
          $SkipT
            && $WIBE
            ->Log('Активация продукта >> ', 3)
            ->checkedChain($WIBE->Enable($WIBE->getFullDataElementRoot()->ID))
            ->Log('<< ----', 3)
            #
          ;
        }
      }

      $WIB->Log('END ELEMENT ----/\----------------------------');

      // if ((microtime(true) - $LoopTime) > EX1C_ELEMENT_MAX_TSEC)
      //   $WIB->Log('END REJECT -----------------------------------' . microtime(true) - $LoopTime)
      //     && (throw new Exception('Execution time exceeded', -2));
    }
  }

  $WIB->Log('--\/------------------------------------------');
  $WIB->Log('RUN DELAY_FN ---------------------------------');

  WorkshopUnity::callDelayFn();

  $WIB->Log('END ------------------------------------------');

  echo 'success' . PHP_EOL;
  echo "Rework $COUNTER products." . PHP_EOL;

  #
} catch (\Throwable $th) {
  if ($th->getCode() === -2)
    throw $th;
  else
    echo ''
      . 'error' . PHP_EOL
      . $th->getMessage() . PHP_EOL
      . $th->__toString() . PHP_EOL;
}
#endregion

#-