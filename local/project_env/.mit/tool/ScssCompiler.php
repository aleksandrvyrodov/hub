<?php

namespace MIT\Tool;

use Closure;
use Iterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\CompilationResult;
use SplFileInfo;

final class ScssCompiler
{
  const EXTENSION = 'scss';
  const UNION_FILE = 'styles.min.css';

  const UINION_MODE_OFF = 1;
  const UINION_MODE_ON = 2;
  // const UINION_MODE_MANUAL = 3;

  static public bool $DEV_MODE = false;
  static public bool $MAP_FILE = false;

  private string $to_path;
  private string $from_path;
  private string $root_path;
  private int $union;

  private array $CssFiles = [];
  private array $ScssFiles = [];

  private Compiler $Scss;

  function __construct(string $from_path, string $to_path, string $root_path, array $list_scss_files = [], int $union = self::UINION_MODE_OFF)
  {
    $this->to_path = $to_path;
    $this->from_path = $from_path;
    $this->root_path = $root_path;
    $this->union = $union;
    $this->mountScssFiles($list_scss_files);

    $this->Scss = new Compiler();
    $this->Scss
      ->setOutputStyle(OutputStyle::COMPRESSED);
    $this->Scss
      ->setImportPaths($this->root_path . $this->from_path);
  }

  private function mountScssFiles(array $list_scss_files)
  {
    foreach ($list_scss_files as $scss_files)
      $this->ScssFiles[] = new SplFileInfo($this->root_path . $this->from_path . '/' . $scss_files);
  }

  public function getFileIterator(): Iterator
  {
    $dir = new RecursiveDirectoryIterator($this->root_path . $this->from_path);

    $files = new \RecursiveCallbackFilterIterator($dir, function ($current, $key, $iterator) {
      if ($iterator->hasChildren())
        return true;
      if ($current->isFile() && str_ends_with($current->getFileName(), '.scss'))
        return true;

      return false;
    });

    return new RecursiveIteratorIterator($files);
  }

  public function __invoke($silent = false)
  {
    try {
      switch ($this->union) {
        case self::UINION_MODE_OFF:
          foreach ($this->getFileIterator() as $ScssFile)
            if ($this->checkUpdate($ScssFile, ($CssFile = $this->genNewPathname($ScssFile, 'min.css', '/css'))))
              $this->compile($ScssFile, $CssFile);
          break;
        case self::UINION_MODE_ON:
          $list_ScssFile = [];
          $update = false;
          $CssFile = new SplFileInfo(implode('/', [
            $this->root_path . $this->to_path,
            self::UNION_FILE,
          ]));

          foreach ($this->getFileIterator() as $ScssFile)
            $update |= $this->checkUpdate(($list_ScssFile[] = $ScssFile), $CssFile);

          if ($update || 1)
            $this->compileUnion($list_ScssFile, $CssFile);

          break;
      }
    } catch (\Throwable $th) {
      if(!$silent)
        throw $th;
    }
  }

  private function genNewPathname(SplFileInfo $ScssFile, string $ext = '', string $path = ''): SplFileInfo
  {
    static $l_extension;
    $l_extension ??= strlen(self::EXTENSION);

    $file_name_scss = $ScssFile->getFilename();
    $file_name_css = substr(
      $file_name_scss,
      0,
      strlen($file_name_scss) - $l_extension
    ) . $ext;

    return new SplFileInfo(implode('/', [
      $this->root_path . $this->to_path,
      trim($path, '/'),
      $file_name_css
    ]));
  }

  private function checkUpdate(SplFileInfo $ScssFile, SplFileInfo $CssFile): bool
  {
    if (!$CssFile->isFile())
      return true;

    if ($ScssFile->getMTime() > $CssFile->getMTime())
      return true;

    return false;
  }

  public function convertMapFilename(SplFileInfo $CssFile): SplFileInfo
  {
    return new SplFileInfo($this->to_path . '/map/' . $CssFile->getFilename() . '.map');
  }

  private function compile(SplFileInfo $ScssFile, SplFileInfo $CssFile): void
  {
    $Scss = $this->Scss;

    $fn_DMode = fn ($_) => true;

    if (self::$DEV_MODE) {
      if (self::$MAP_FILE) {
        $Scss->setSourceMap(Compiler::SOURCE_MAP_FILE);
        $CssMapFile = $this->genNewPathname($ScssFile, 'css.map', 'map');
        $Scss->setSourceMapOptions([
          'sourceMapWriteTo'  => $CssMapFile,
          'sourceMapURL'      => $this->to_path . '/map' . '/' . $CssMapFile->getFilename(),
          'sourceMapFilename' => $this->to_path . '/css' . '/' . $CssFile->getFilename(),
          'sourceMapBasepath' => $this->root_path . $this->from_path,
          'sourceMapRootpath' => $this->from_path . '/',
        ]);
        $fn_DMode = fn ($ScssResult) => file_put_contents(
          $CssMapFile,
          $ScssResult->getSourceMap()
        );
      } else {
        $Scss->setSourceMap(Compiler::SOURCE_MAP_INLINE);
        $Scss->setSourceMapOptions([
          'sourceMapBasepath' => $this->root_path . $this->from_path,
          'sourceMapRootpath' => $this->from_path . '/',
        ]);
      }
    }


    $ScssResult = $Scss->compileString(file_get_contents($ScssFile), $ScssFile);

    file_put_contents(
      $CssFile,
      $ScssResult->getCss()
    ) && $this->CssFiles[] = $CssFile;

    $fn_DMode($ScssResult);
  }

  private function compileUnion(array $list_ScssFile, SplFileInfo $CssFile): void
  {
    $Scss = $this->Scss;
    $fn_DMode = fn ($_) => true;

    if (self::$DEV_MODE) {
      if (self::$MAP_FILE) {
        $Scss->setSourceMap(Compiler::SOURCE_MAP_FILE);
        $CssMapFile = new SplFileInfo(implode('/', [
          $this->root_path . $this->to_path . '/map',
          self::UNION_FILE . '.map',
        ]));
        $Scss->setSourceMapOptions([
          'sourceMapWriteTo'  => $CssMapFile,
          'sourceMapURL'      => $this->to_path . '/map' . '/' . $CssMapFile->getFilename(),
          'sourceMapFilename' => $this->to_path . '/css' . '/' . $CssFile->getFilename(),
          'sourceMapBasepath' => $this->root_path . $this->from_path,
          'sourceMapRootpath' => $this->from_path . '/',
        ]);
        $fn_DMode = fn ($ScssResult) => file_put_contents(
          $CssMapFile,
          $ScssResult->getSourceMap()
        );
      } else {
        $Scss->setSourceMap(Compiler::SOURCE_MAP_INLINE);
        $Scss->setSourceMapOptions([
          'sourceMapBasepath' => $this->root_path . $this->from_path,
          'sourceMapRootpath' => $this->from_path . '/',
        ]);
      }
    }

    $len_from_path = strlen($this->root_path . '/' . $this->from_path);
    $content = '';

    !empty($this->ScssFiles)
      && $list_ScssFile = $this->ScssFiles;

    foreach ($list_ScssFile as $ScssFile)
      $content .= '@import "' . substr(
        $ScssFile->getPathname(),
        $len_from_path
      ) . '";' . PHP_EOL;

    $ScssResult = $Scss->compileString($content);

    file_put_contents(
      $CssFile,
      $ScssResult->getCss()
    );

    $fn_DMode($ScssResult);
  }
}
