<?php

namespace MIT\Model\FavoriteProduct;

interface IStorage
{
  public static function Init(): IStorage;

  public function getList(bool $self_load = Storage::SELF_LOAD): array;

  public function add(int $id_product): bool;
  public function addList(array $list_id_product): bool;
  public function delete(int $id_product): bool;
  public function deleteList(array $list_id_product): bool;

  public function issetProduct(int $product_id): bool;
}
