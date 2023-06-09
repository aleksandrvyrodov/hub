<?php

namespace MIT\App\Postgrabber\Model;

final class PostRSS
{
  public bool    $home = true;

  public function __construct(
    public int     $time,
    public string  $title,
    public string  $code,
    public string  $descr,
    public string  $thumb,
    public string  $content,
    public array   $tags = [],
    public ?string  $hash = null,
  ) {
    empty($hash)
      && $this->hash = self::Hash($time, $title);
  }

  static public function Hash(int $time, string $title): string
  {
    return md5(
      (string)$time . $title
    );
  }
}
