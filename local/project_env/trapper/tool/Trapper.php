<?php

namespace MIT\Tool;


interface ITrapperStoraged
{
  public function s_post(): ITrapperStoraged;
  public function s_session(): ITrapperStoraged;
  public function s_cookie(): ITrapperStoraged;
  public function s_get(): ITrapperStoraged;
  public function s_input(): ITrapperStoraged;
  public function s_request(): ITrapperStoraged;
  public function s_files(): ITrapperStoraged;
  public function s_headers(): ITrapperStoraged;
  public function s_server(): ITrapperStoraged;
}

class Assist
{
  const PHP_INIT_CONTENT = <<<HEAD
    <?php


    /*  ================================================================  */
    HEAD;

  static public function RotateFiles($in)
  {
    $files = array();

    foreach ($in as $FILE) {
      $gi = count($files);
      $diff = count($FILE) - count($FILE, COUNT_RECURSIVE);

      if ($diff == 0)
        $files[$gi] = $FILE;
      else
        foreach ($FILE as $k => $l) {
          foreach ($l as $i => $v) {
            $files[$i + $gi][$k] = $v;
          }
        }
    }


    return $files;
  }

  static public function CleanEOL($in)
  {
    return preg_replace('/(\r\n|\n|\r)/', ' ', $in);
  }
}

abstract class TrapperStoraged implements ITrapperStoraged
{
  const FORCE_SAVE = true;
  const DELAY_SAVE = false;
  // FIX
  const PLACE_SAVE = '';
  const DIR_NAME = '.term';
  const DIR_NAME_TEMP = 'seclusion';
  const FILE_PHP = 'caught.php';

  protected $storage = [];
  protected $content = [];

  protected $PATH = self::PLACE_SAVE . '/' . self::DIR_NAME;
  protected $FILE = self::FILE_PHP;
  protected $DIR_NAME_TEMP = self::DIR_NAME_TEMP;

  /* public function define_file($filename){
    if(file)
  } */

  public function setSettings(string $path, string $file, bool $mount = false): static
  {
    static $inited;

    if (empty($inited) && $inited = true) {
      $this->PATH = $path . '/' . self::DIR_NAME;
      $this->FILE = $file;
    }

    if ($mount) {
      if (!file_exists($this->PATH)) {
        if (!mkdir($this->PATH, 0744, true))
          throw new \Exception("Error created folder", 1);
        if (!mkdir($this->PATH . '/' . $this->DIR_NAME_TEMP, 0744))
          throw new \Exception("Error created temp folder", 1);
      }

      if (!file_exists($this->PATH . '/' . $this->FILE)) {
        if (file_put_contents($this->PATH . '/' . $this->FILE, Assist::PHP_INIT_CONTENT) === false)
          throw new \Exception("Error created file", 1);
      }
    }

    return $this;
  }

  protected function _save()
  {
    $this->content[1] .= var_export($this->storage, true);
    $this->content = implode('', $this->content);

    file_put_contents(
      "{$this->PATH}/{$this->FILE}",
      $this->content,
      FILE_APPEND
    );
  }

  public function save(bool $forse = self::FORCE_SAVE): ?\Closure
  {
    if ($forse)
      $this->_save();
    else
      return $this->_save(...);

    return null;
  }

  #region ITrapperStoraged
  public function s_post(): self
  {
    $this->storage['POST'] = empty($_POST) ? null : $_POST;
    return $this;
  }

  public function s_session(): self
  {
    $this->storage['SESSION'] = $_SESSION ?? '~SESSION_OFF';
    return $this;
  }

  public function s_cookie(): self
  {
    $this->storage['COOKIE'] = empty($_COOKIE) ? null : $_COOKIE;
    return $this;
  }

  public function s_get(): self
  {
    $this->storage['GET'] = empty($_GET) ? null : $_GET;
    return $this;
  }

  public function s_input(): self
  {
    return $this->s_content(
      file_get_contents('php://input'),
      'INPUT',
      'input'
    );
  }

  public function s_content(?string $content = null, string $mark = 'CONTENT',  string $ext = 'txt'): self
  {
    if (!empty($content)) {
      $file = md5(date('y_m_d__H_i_s') . hrtime(true)) . (empty($ext) ? '' : ".$ext");
      $path = $this->PATH . '/'
        . $this->DIR_NAME_TEMP;

      file_put_contents($path . '/' . $file, $content);
      $this->storage[$mark] = $file;
    } else
      $this->storage[$mark] = null;

    return $this;
  }

  public function s_request(): self
  {
    $this->storage['REQUEST'] = empty($_REQUEST) ? null : $_REQUEST;
    return $this;
  }

  public function s_files(): self
  {
    $FILES = Assist::RotateFiles($_FILES);

    if (!empty($FILES))
      foreach ($FILES as $FILE) {
        $name = md5(date('y_m_d__H_i_s') . hrtime(true) . (string)rand());
        $file = $name . '.file';
        $path = $this->PATH . '/'
          . $this->DIR_NAME_TEMP;

        $this->storage['FILES'][$name] = $FILE;

        if (!$FILE['error'])
          $this->storage['FILES'][$name]['~COPY']
            = copy($FILE['tmp_name'], $path . '/' . $file);
      }
    else
      $this->storage['FILES'] = null;

    return $this;
  }

  public function s_headers(): self
  {
    $this->storage['HEADERS'] = getallheaders();
    return $this;
  }

  public function s_headers_response(): self
  {
    $this->storage['HEADERS_RESPONSE'] = headers_list();
    return $this;
  }

  public function s_server(): self
  {
    $this->storage['SERVER'] = $_SERVER;
    return $this;
  }
  #endregion

}

class Trapper extends TrapperStoraged
{
  public function catchRequest()
  {
    $this->set_head();

    $this
      ->s_headers()
      ->s_server()
      ->s_get()
      ->s_post()
      ->s_files()
      ->s_request()
      ->s_input()
      ->s_session()
      ->s_cookie();

    $this->save();

    return $this;
  }

  public function catchManualRequest(\Closure ...$fn_list_storage_request)
  {
    $this->set_head();

    foreach ($fn_list_storage_request as $fn_storage_request)
      $fn_storage_request();

    return $this;
  }

  private function set_head()
  {
    $this->content[0] = PHP_EOL . PHP_EOL;
    $this->content[1] = '$DT___' . date('y_m_d__H_i_s') . '___' . hrtime(true) . ' = ';
    $this->content[2] = ';' . PHP_EOL . PHP_EOL;
    $this->content[3] = '/*  ================================================================  */';

    return $this;
  }
}
