<?php

namespace MIT;

class Loader
{

  private array $list_Model = [];
  private array $list_Module = [];
  private array $list_Component = [];

  private static Loader $Loader;

  private function __construct()
  {
  }

  public static function Init()
  {
    if (empty(self::$Loader))
      self::$Loader = new self();

    return self::$Loader;
  }

  public function loadModule($name): bool
  {
    $fn_include = fn ($name) => ($this->list_Module[$name] = \CModule::IncludeModule($name));

    if (array_key_exists($name, $this->list_Module)) {
      if ($this->list_Module[$name])
        return true;
      else
        return $fn_include($name, $this);
    } else
      return $fn_include($name, $this);
  }

  public function loadComponent($name)
  {
    if (!array_key_exists($name, $this->list_Component)) {
      $this->list_Component[$name] = new \CBitrixComponent();
      $this->list_Component[$name]->InitComponent($name);
    }

    return $this->list_Component[$name];
  }

  public function loadModel($name): string
  {
    if (!array_key_exists($name, $this->list_Model)) {
      if (file_exists(__DIR__ . "/model/$name.php")) {

        $str_Model = '\\MIT\\Model\\' . $name;

        if (array_key_exists('MIT\Model\IIncludeDependencies', class_implements($str_Model))) {
          if ($str_Model::Dep($this))
            $this->list_Model[$name] = $str_Model;
          else
            throw new \Exception("[MIT] Fail load dependenses", 1);
        } else
          throw new \Exception("[MIT] Need use inteface IIncludeDependencies for your model", 1);
      } else
        throw new \Exception("[MIT] Undefinded model", 1);
    }

    return $this->list_Model[$name];
  }
}
