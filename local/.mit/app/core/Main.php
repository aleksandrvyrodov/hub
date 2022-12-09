<?php

namespace MIT\App\Core;

use MIT\App\Core\Exception\ComposerNotFountException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Main
{
  private $composerException;

  public function __construct()
  {
    $this->composerException = new  \MIT\App\Core\Exception\ComposerNotFountException('Установите модули (composer install)');
  }

  public function includeLibAll()
  {
    $this->initWhoops();
  }

  public function initWhoops()
  {
    // if ($debugConfig['debug']) {

      if (!class_exists(Run::class)) {
        throw $this->composerException;
      }

      error_reporting(E_ALL & ~E_NOTICE);

      $handler = new PrettyPageHandler;
      $handler->setEditor(fn ($file, $line) => "vscode://file$file:$line");
      // $handler->setEditor("vscode");

      $Whoops = new Run;

      $Whoops->pushHandler($handler);
      $Whoops->register();
    // }
  }
}
