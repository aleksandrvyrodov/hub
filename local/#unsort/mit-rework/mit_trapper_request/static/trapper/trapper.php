<?php

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

abstract class TrapperStoraged implements ITrapperStoraged
{
  protected $storage = [];
  protected $content = [];

  protected $PATH = MIT_PATH . '/.term';
  protected $FILE = 'caught.php';

  public function s_post(): self
  {
    $this->storage['POST'] = $_POST;
    return $this;
  }

  public function s_session(): self
  {
    $this->storage['SESSION'] = $_SESSION;
    return $this;
  }

  public function s_cookie(): self
  {
    $this->storage['COOKIE'] = $_COOKIE;
    return $this;
  }

  public function s_get(): self
  {
    $this->storage['GET'] = $_GET;
    return $this;
  }

  public function s_input(): self
  {
    $input_raw = file_get_contents('php://input');

    $this->storage['INPUT'] = [];
    $this->storage['INPUT']['RAW'] = Assist::CleanEOL($input_raw);

    ($input_json = json_decode($this->storage['INPUT']['RAW'], true))
      && $this->storage['INPUT']['JSON'] = $input_json;

    return $this;
  }

  public function s_request(): self
  {
    $this->storage['REQUEST'] = $_REQUEST;
    return $this;
  }

  public function s_files(): self
  {
    $FILES = Assist::RotateFiles($_FILES);

    foreach ($FILES as $FILE) {
      $name = md5(time() . $FILE['name'] . (string)rand());
      $this->storage['FILES'][$name] = $FILE;

      if (!$FILE['error'])
        move_uploaded_file($FILE['tmp_name'], $this->PATH . '/files/' . $name);
    }

    return $this;
  }

  public function s_headers(): self
  {
    $this->storage['HEADERS'] = getallheaders();
    return $this;
  }

  public function s_server(): self
  {
    $this->storage['SERVER'] = $_SERVER;
    return $this;
  }
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

    $this->content[1] .= var_export($this->storage, true);
    $this->content = implode('', $this->content);

    file_put_contents(
      "{$this->PATH}/{$this->FILE}",
      $this->content,
      FILE_APPEND
    );

    return $this;
  }

  public function catchManualRequest(Closure ...$fn_list_storage_request)
  {
    $this->set_head();

    foreach ($fn_list_storage_request as $fn_storage_request)
      $fn_storage_request();


    $this->content[1] .= var_export($this->storage, true);
    $this->content = implode('', $this->content);

    file_put_contents(
      "{$this->PATH}/{$this->FILE}",
      $this->content,
      FILE_APPEND
    );

    return $this;
  }

  private function set_head()
  {
    $this->content[0] = PHP_EOL . PHP_EOL;
    $this->content[1] = '$DT___' . date('y_m_d__H_i_s') . ' = ';
    $this->content[2] = ';' . PHP_EOL . PHP_EOL;
    $this->content[3] = '/*  ================================================================  */';

    return $this;
  }
}
