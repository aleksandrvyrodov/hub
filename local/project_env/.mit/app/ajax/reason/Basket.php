<?php

namespace MIT\App\Ajax\Reason;

use MIT\Loader;
use MIT\Model\QuickBasket;

class Basket
{
  private $request;
  private QuickBasket $QuickBasket;

  public function __construct($request)
  {
    $this->request = $request;

    /**
     * @var MIT\Model\QuickBasket $QuickBasket
     */
    $this->QuickBasket = Loader::Init()
      ->loadModelInit('QuickBasket');
  }

  public function file()
  {
    try {
      $QuickBasket = $this->QuickBasket;

      if ($rows = $QuickBasket::ParseXlsx($_FILES['file']['tmp_name'])) {
        $arXlsOrder = $QuickBasket::SorterRawRows($rows);

        if (!empty($arXlsOrder['ARTICLE']))
          $QuickBasket::ConvertArticleToXmlId($arXlsOrder);

        if (empty($arXlsOrder['XML']))
          return $this->pack(false, __METHOD__);

        $s_rows = $QuickBasket::GetStructuredRows($arXlsOrder);

        if (empty($s_rows))
          return $this->pack(false, __METHOD__);

        // $QuickBasket::ClearBasket();
        $RES = $QuickBasket::AddBasket($s_rows);

        return $this->pack(
          $RES,
          __METHOD__
        );
      }

      return $this->pack(
        false,
        __METHOD__
      );
    } catch (\Throwable $th) {
      throw new \Exception('Fail in action [' . __METHOD__ . ']' . $th->getMessage(), 1, $th);
    }
  }






  /**************************************************************** */




  private function pack($res, $action)
  {
    return [
      'result' => $res,
      'action' => explode('::', $action)[1],
    ];
  }
}
