<?php

namespace MIT\App\Ajax\Reason;

use MIT\Loader;

class Favorite
{
  private $request;
  private \MIT\Model\FavoriteProduct\IStorage $Storage;

  public function __construct($request)
  {
    $this->request = $request;

    /**
     * @var MIT\Model\FavoriteProduct $FavoriteProduct
     */
    $FavoriteProduct = Loader::Init()
      ->loadModelInit('FavoriteProduct');
    $this->Storage = $FavoriteProduct->initStorage();
  }

  public function add()
  {
    try {
      $id = (int)$this->request['id'];
      if (!empty($id))
        return $this->pack(
          $this
            ->Storage
            ->add($id),
          __METHOD__
        ) + ['count' => $this->_count()];
      else
        return $this->pack(false, __METHOD__);
    } catch (\Throwable $th) {
      throw new \Exception('Fail in action [' . __METHOD__ . ']' . $th->getMessage(), 1, $th);
    }
  }

  public function delete()
  {
    try {
      $id = (int)$this->request['id'];
      if (!empty($id))
        return $this->pack(
          $this
            ->Storage
            ->delete($id),
          __METHOD__
        ) + ['count' => $this->_count()];
      else
        return $this->pack(false, __METHOD__);
    } catch (\Throwable $th) {
      throw new \Exception('Fail in action [' . __METHOD__ . ']' . $th->getMessage(), 1, $th);
    }
  }

  private function _count()
  {
    return count($this->_list());
  }

  public function count()
  {
    try {
      return $this->pack(
        $this->_count(),
        __METHOD__
      );
    } catch (\Throwable $th) {
      throw new \Exception('Fail in action [' . __METHOD__ . ']' . $th->getMessage(), 1, $th);
    }
  }

  private function _list()
  {
    return $this
      ->Storage
      ->getList();
  }

  public function list()
  {
    try {
      return $this->pack(
        $this->_list(),
        __METHOD__
      );
    } catch (\Throwable $th) {
      throw new \Exception('Fail in action [' . __METHOD__ . ']' . $th->getMessage(), 1, $th);
    }
  }

  private function pack($res, $action)
  {
    return [
      'result' => $res,
      'action' => explode('::', $action)[1],
    ];
  }
}
