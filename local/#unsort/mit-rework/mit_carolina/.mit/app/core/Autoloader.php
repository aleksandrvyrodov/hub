<?php

namespace MIT\App\Core;

class Autoloader
{

  protected $prefixes = array();

  public function register()
  {
    spl_autoload_register(array($this, 'loadClass'));
  }

  public function addNamespace($prefix, $base_dir, $prepend = false)
  {
    $prefix = trim($prefix, '\\') . '\\';
    $base_dir = rtrim($base_dir, DIRECTORY_SEPARATOR) . '/';

    if (isset($this->prefixes[$prefix]) === false)
      $this->prefixes[$prefix] = array();

    if ($prepend)
      array_unshift($this->prefixes[$prefix], $base_dir);
    else
      array_push($this->prefixes[$prefix], $base_dir);

    return $this;
  }

  public function loadClass($class)
  {
    $prefix = $class;

    while (false !== $pos = strrpos($prefix, '\\')) {
      $prefix = substr($class, 0, $pos + 1);
      $relative_class = substr($class, $pos + 1);
      $mapped_file = $this->loadMappedFile($prefix, $relative_class);

      if ($mapped_file)
        return $mapped_file;

      $prefix = rtrim($prefix, '\\');
    }

    return false;
  }

  protected function loadMappedFile($prefix, $relative_class)
  {
    if (isset($this->prefixes[$prefix]) === false)
      return false;

    foreach ($this->prefixes[$prefix] as $base_dir) {
      $chunk = explode('\\', $relative_class);
      $file_name = array_pop($chunk) . '.php';
      $step = 1;
      do {
        if ($step)
          $path_full = strtolower(implode('/', $chunk));
        else
          $path_full = implode('/', $chunk);

        $relative_path = $chunk
          ? $path_full . '/'
          : '';

        $file = $base_dir
          . $relative_path
          . $file_name;

        if ($this->requireFile($file))
          return $file;
      } while ($step--);
    }

    return false;
  }

  protected function requireFile($file)
  {
    if (file_exists($file)) {
      require $file;
      return true;
    }
    return false;
  }
}
