<?php

#region Include
use MIT\Model\CarolinaPropertyFilter;
use MIT\Model\ParamCopyIBlock;
use MIT\Model\WorkshopIBlock;
use MIT\Model\WorkshopIBlockCatalog;
use MIT\Model\WorkshopIBlockElementCollector;
use MIT\Model\WorkshopIBlockElement;
use MIT\Model\WorkshopIBlockProduct;
use MIT\Model\FullDataElement;
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

  $fn_HeartOfTarrasque = function (FullDataElement $FullDataElementIn, bool $abyssalBlade = false, bool $checkRoot = true) use ($WIB, &$FullDataElementMain, &$COUNTER, &$COUNTER_J): void {
    if ($FullDataElementMain)
      $RootIdXML = explode('!', $FullDataElementMain->XML_ID)[1];
    else
      $RootIdXML = null;
    $phantasm = (int)(($abyssalBlade && !$FullDataElementMain) || (!!$RootIdXML && $RootIdXML == $FullDataElementIn->XML_ID));

    do {
      $LoopTime = microtime(true);

      $WIB->Log('START ELEMENT --\/----------------------------' . (!$phantasm ?: ' PH'), 1);
      $FullDataElement = $FullDataElementIn;

      try {
        $WIBE = new WorkshopIBlockElement($WIB, $FullDataElement);

        if ($phantasm)
          $WIBE
            ->Log("#[$COUNTER|R]", 1)
            ->Log("Получен элемент ([" . $FullDataElement->ID . '] ' . $FullDataElement->NAME . ')', 2)
            ->Log('Проверка элемента >>', 2)
            ->onlyChain($checkRoot && $WIBE->CheckChangeElement_D(MIT\Shell\LAST_MOD_TIME))
            ->Log('Копирование элемента >>', 2)
            ->checkedChain($WIBE->CopyElement_D($FullDataElement))
            ->Log('Копирование свойств >>', 2)
            ->checkedChain($WIBE->CopyElementProperty_D($FullDataElement), false)
            && (throw new Exception("Create dublicate root", -4));

        $WIBE
          ->Log("#[$COUNTER|" . $COUNTER_J++ . ']', 1)
          ->Log("Получен элемент ([" . $FullDataElement->ID . '] ' . $FullDataElement->NAME . ')', 2)
          ->Log('Проверка элемента >>', 2)
          ->CheckChangeElement(MIT\Shell\LAST_MOD_TIME, (bool) $FullDataElementMain)
          ->Log('<< ----', 2)
          ->Log('Копирование элемента >>', 2)
          ->checkedChain($WIBE->CopyElement($FullDataElementMain, true))
          ->Log('<< ----', 2)
          ->Log('Копирование свойств >>', 2)
          ->checkedChain($WIBE->CopyElementProperty(), false)
          ->Log('<< ----', 2)
          #
        ;

        $WIBP = new WorkshopIBlockProduct($WIBE);
        $WIBP
          ->Log('Копирование основной информации продукта >>', 3)
          ->checkedChain($WIBP->CopyProductMain())
          ->Log('<< ----', 3)
          ->Log('Копирование информации о складах продукта >>', 3)
          ->checkedChain($WIBP->CopyProductStore(), false)
          ->Log('<< ----', 3)
          ->Log('Копирование информации о прайсах продукта >>', 3)
          ->checkedChain($WIBP->CopyProductPrice(), false)
          ->Log('<< ----', 3)
          #
        ;

        $WIBE
          ->Log('Монтирование SKU >>', 3)
          ->checkedChain($WIBE->MountProductSKU())
          ->Log('<< ----', 4)
          #
        ;

        $WIBE
          ->Log('Активация SKU >>', 4)
          ->checkedChain($WIBE->Enable($WIBE->getFullDataElementTarget()->ID))
          ->Log('<< ----', 4)
          #
        ;

        $COUNTER++;
        $SkipT = true;
      } catch (\Throwable $th) {
        if (($SkipT = ($th->getCode() === -2)) || $th->getCode() === -3)
          $WIB->Log('SKIP > ' . $th->getMessage(), 2);
        elseif (($SkipT = ($th->getCode() === -4)))
          $WIB->Log('ROOT > ' . $th->getMessage(), 2);
        else
          $WIB->Log('ERROR > ' . $th->getMessage(), 2);
      } finally {
        if ($FullDataElementMain)
          $SkipT
            && $WIBE
            ->Log('Деактивация продукта >> ', 4)
            ->checkedChain($WIBE->Disable($FullDataElement->ID))
            ->Log('<< ----', 4)
            #
          ;
        else {
          $FullDataElementMain = $FullDataElement;
          $SkipT
            && $WIBE
            ->Log('Активация продукта >> ', 4)
            ->checkedChain($WIBE->Enable($FullDataElement->ID))
            ->Log('<< ----', 4)
            #
          ;
        }
      }

      $WIB->Log('END ELEMENT ----/\----------------------------', 1);

      // if ((microtime(true) - $LoopTime) > EX1C_ELEMENT_MAX_TSEC)
      //   $WIB->Log('END REJECT -----------------------------------' . microtime(true) - $LoopTime)
      //     && (throw new Exception('Execution time exceeded', -2));
    } while ($phantasm--);
  };

  $fn_DivineRapier = function (FullDataElement $FullDataElement) use (&$FullDataElementMain): void {
    $FullDataElementMain = $FullDataElement;
  };

  $fn_AghanimsScepter =  function ($FullDataElement, &$StackDelayedElement, \Closure $fn_Cond, bool $mode) use ($fn_HeartOfTarrasque, $fn_DivineRapier, $WIB, &$FullDataElementMain, &$COUNTER, &$COUNTER_J) {
    if (!$FullDataElementMain) {
      $__ = null;
      $WIB->Log('MODE STASH', 1);

      if ($fn_Cond($__)) {
        $WIB->Log('FIND ' . (!$mode ?: 'NAT') . ' ROOT ELEMENT --\/----------------------------', 1);
        if ($mode) {
          $fn_DivineRapier($FullDataElement);
          $WIB->Log('DIVINE RAPIER ' . ($__[1] ?? null), 1);
        } else
          $fn_HeartOfTarrasque($FullDataElement, true);

        $WIB->Log('FLUSH STASH_' . ($mode ? 'UP' : 'DOWN') . ' --\/----------------------------', 1);
        while ($FullDataElement = array_pop($StackDelayedElement))
          $fn_HeartOfTarrasque($FullDataElement);

        $WIB->Log('END FLUSH STASH_' . ($mode ? 'UP' : 'DOWN') . ' --/\L----------------------------', 1);
      } else {
        $WIB->Log("RECORD STASH_" . ($mode ? 'UP' : 'DOWN') . " [{$FullDataElement->ID}]", 1);
        $StackDelayedElement[] = $FullDataElement;
      }
    } else {
      $WIB->Log('MODE NORMALY', 1);
      $fn_HeartOfTarrasque($FullDataElement);
    }
  };

  foreach ($WIBEC->CollectorData() as $CollectorDataElement) {
    $FullDataElementMain = null;
    $RootIdXML = null;
    $StackDelayedElementUp = [];
    $StackDelayedElementDown = [];

    $WIB->Log('ENTRY GROUP --\/S---- (' . $CollectorDataElement->NAME . ')');
    foreach ($WIBEC->FullData($CollectorDataElement->NAME) as $FullDataElement) {
      $fn_AghanimsScepter(
        $FullDataElement,
        $StackDelayedElementUp,
        fn (&$__ = null) => ($__ = explode('!', $FullDataElement->XML_ID))[0] == 'D',
        true
      );
    }

    if (!!$StackDelayedElementUp) {
      $WIB->Log('UNFIND NAT ROOT ELEMENT', 1);
      $WIB->Log('OFFSET ID [' . array_reduce($StackDelayedElementUp, fn ($c, $El) => ($c . ', ' . $El->ID), 'E') . ']', 2);

      while ($d_FullDataElement = array_pop($StackDelayedElementUp)) {
        $fn_AghanimsScepter(
          $d_FullDataElement,
          $StackDelayedElementDown,
          fn ($_ = null) => $FullDataElement->YAVLYAETSYABAZOVOY,
          false
        );
      }
    }

    if (!!$StackDelayedElementDown) {
      $WIB->Log('UNFIND ROOT ELEMENT', 1);
      $WIB->Log('OFFSET ID [' . array_reduce($StackDelayedElementDown, fn ($c, $El) => ($c . ', ' . $El->ID), 'E') . ']', 2);
      $phantasm = 0;

      while ($d_FullDataElement = array_pop($StackDelayedElementDown))
        $fn_HeartOfTarrasque($d_FullDataElement, !($phantasm++), false);
    }

    $WIB->Log('END GROUP --/\F----------------------------');

    break;
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