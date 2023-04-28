<?php

namespace MIT\Model;

use MIT\App\Postgrabber\Model\PostRSS;
use MIT\Loader;

class ImportPost implements IIncludeDependencies, ISingleton
{

  const NEWS_IBLOCK_ID = 20;
  const NEWS_SECTION_ID = 154;
  const NEWS_PROP_SIP = 84;
  const STOCK_IBLOCK_ID = 24;
  const STOCK_SECTION_ID = 0;
  const STOCK_PROP_SIP = 115;

  const BRAND_IBLOCK_ID = 30;
  const TRIGGER_STOCK = 'АКЦИИ';

  private static ImportPost $ImportPost;
  private static $list_PostRSS = [];


  public static function Dep(Loader $Loader): bool
  {
    // try {
    $res = 1
      && $Loader->loadModule('iblock');

    // } catch (LoaderException $le) {
    # LOG: failed to load dependencies
    // } finally{
    //   return $res;
    // }
    return $res;
  }

  public static function Init(): static
  {
    if (empty(self::$ImportPost))
      self::$ImportPost = new self();

    return self::$ImportPost;
  }

  private function __construct()
  {
  }

  static private function CheckPostIBlock(PostRSS $PostRSS): bool
  {
    return (bool)(\CIblockElement::GetList(
      [],
      ["IBLOCK_ID" => [self::NEWS_IBLOCK_ID, self::STOCK_IBLOCK_ID], "PROPERTY_IMPORT_HASH" => $PostRSS->hash],
      false,
      false,
      ["ID"]
    ))->GetNextElement();
  }

  static private function SwitchPostIBlock(PostRSS $PostRSS): object
  {
    $fn_Params = fn (...$arg): object => new class(...$arg)
    {
      public function __construct(
        public int $IBLOCK_ID,
        public int $IBLOCK_SECTION_ID,
      ) {
      }
    };

    if (in_array(self::TRIGGER_STOCK, $PostRSS->tags))
      return $fn_Params(self::STOCK_IBLOCK_ID, self::STOCK_SECTION_ID);
    else
      return $fn_Params(self::NEWS_IBLOCK_ID, self::NEWS_SECTION_ID);
  }

  static private function LinkedBrandsID(PostRSS $PostRSS): array
  {

    $res = \CIblockElement::GetList(
      [],
      ["IBLOCK_ID" => self::BRAND_IBLOCK_ID, "NAME" => $PostRSS->tags],
      false,
      false,
      ["ID"]
    );

    $list_BrandID = [];

    while ($ID = (int)$res->fetch()['ID'])
      $list_BrandID[] = $ID;

    return $list_BrandID;
  }

  static private function AddPostIBlock(PostRSS $PostRSS, int $IBLOCK_ID, int $IBLOCK_SECTION_ID = 0, array $list_BRAND_ID = []): int
  {
    $arFields = array(
      'ACTIVE' => 'Y',
      'ACTIVE_FROM' => date('d.m.Y H:i:s', $PostRSS->time),
      'IBLOCK_ID' => $IBLOCK_ID,
      'IBLOCK_SECTION_ID' => $IBLOCK_SECTION_ID,
      'NAME' => $PostRSS->title,
      'CODE' => $PostRSS->code,
      'PREVIEW_TEXT' => $PostRSS->descr,
      'PREVIEW_TEXT_TYPE' => 'text',
      'PREVIEW_PICTURE' => \CFile::MakeFileArray($PostRSS->thumb),
      'DETAIL_TEXT' => $PostRSS->content,
      'DETAIL_TEXT_TYPE' => 'html',
      'PROPERTY_VALUES' => [
        'IMPORT_HASH' => $PostRSS->hash,
      ]
    );

    $arProps = &$arFields['PROPERTY_VALUES'];

    switch ($IBLOCK_ID) {
      case self::STOCK_IBLOCK_ID:
        $arProps['LINK_BRANDS'] = array_map(fn ($BRAND_ID) => ['VALUE' => $BRAND_ID], $list_BRAND_ID);
        $arProps['SHOW_ON_INDEX_PAGE'] = ['VALUE' => self::STOCK_PROP_SIP];
        break;
      case self::NEWS_IBLOCK_ID:
        $arFields['TAGS'] = implode(',', $PostRSS->tags);
        $arProps['SHOW_ON_INDEX_PAGE'] = ['VALUE' => self::NEWS_PROP_SIP];
        break;
    }

    $obElement = new \CIBlockElement();
    $idElement = (int)$obElement->Add($arFields);

    return $idElement;
  }

  public function addPost(?array $list_PostRSS = null, bool $once = true)
  {
    $list_PostRSS ??= self::$list_PostRSS;

    $insert_post = [];

    foreach ($list_PostRSS as $PostRSS) {
      if (self::CheckPostIBlock($PostRSS)) {
        if ($once && $insert_post !== [])
          break;
        else
          continue;
      }

      $Param = self::SwitchPostIBlock($PostRSS);
      $Args = [
        $PostRSS,
        $Param->IBLOCK_ID,
        $Param->IBLOCK_SECTION_ID,
      ];

      if ($Param->IBLOCK_ID === self::STOCK_IBLOCK_ID)
        $Args[] = $Param->list_BRAND_ID = self::LinkedBrandsID($PostRSS);

      $insert_post[$PostRSS->hash] = self::AddPostIBlock(...$Args);
    }

    return $insert_post;
  }

  static public function PostWithRss($raw_PostRSS): array
  {
    $SXML_PostRSS = new \SimpleXMLElement($raw_PostRSS);

    foreach ($SXML_PostRSS->channel->item as $Item)
      $list_PostRSS[$h = PostRSS::Hash(
        $pb = strtotime((string)$Item->pubDate),
        $t = $Item->title
      )] = new PostRSS(
        title: $t,
        time: $pb,
        code: end(explode('/', trim($Item->link, '/'))),
        descr: (string)$Item->description,
        thumb: (string)$Item->image,
        content: (string)$Item->content,
        tags: array_map(fn ($_) => mb_strtoupper((string)$_), (array)$Item->category),
        hash: $h
      );

    return $list_PostRSS;
  }
}
