<?php

namespace MIT\App\Postgrabber;

use MIT\Loader;
use MIT\Model\ImportPost;
use const MIT\App\Postgrabber\URL;
use function MIT\Function\Junkyard\getDataByLink;

class Run
{
  private static array $result = [];

  static public function Start(): string
  {
    Dep::Load();

    /**
     * @var MIT\Model\ImportPost $ImportPost
     */
    $ImportPost = Loader::Init()
      ->loadModelInit('ImportPost');

    $raw_RSS = getDataByLink(
      URL,
      [
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_FOLLOWLOCATION => 1
      ]
    );

    try {
      $Stack = ImportPost::PostWithRss($raw_RSS);
      self::$result = $ImportPost->addPost($Stack);

      #
    } catch (\Throwable $th) {
      // echo $th->getMessage() . ' --- FAIL!!!' . PHP_EOL;
    } finally {
      return '\MIT\App\Postgrabber\Run::Start();';
    }
  }

  public static function LastInsert(): array
  {
    return self::$result;
  }
}
