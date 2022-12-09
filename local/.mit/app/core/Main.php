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
    $config = include(\MIT\Bitrix\PATH_SETTINGS);
    $debug_config = $config['exception_handling']['value'] ?? false;

    if ($debug_config && $debug_config['debug']) {

      if (!class_exists(Run::class)) {
        throw $this->composerException;
      }

      if (isset($debug_config['handled_errors_types'])) {
        $error_level = ($debug_config['handled_errors_types'] & E_NOTICE)
          ? ($debug_config['handled_errors_types'] & ~E_NOTICE)
          : $debug_config['handled_errors_types'];

        error_reporting($error_level);

        $handler = new PrettyPageHandler;
        $handler->setPageTitle("Ops! Error caught.");
        // $handler->setEditor(fn ($file, $line) => "vscode://file$file:$line");
        // $handler->setEditor("vscode");

        $Whoops = new Run;

        $Whoops->pushHandler($handler);
        $Whoops->register();
      }
    }
  }
}
