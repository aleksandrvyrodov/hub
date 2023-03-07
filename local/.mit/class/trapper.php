<?php

class Trapper
{

  private $storage = [];
  private $content = [];

  private $PATH = MIT_PATH . '/.term';
  private $FILE = 'caught.php';


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

  private function set_head()
  {
    $this->content[0] = PHP_EOL . PHP_EOL;
    $this->content[1] = '$DT___' . date('y_m_d__H_i_s') . ' = ';
    $this->content[2] = ';' . PHP_EOL . PHP_EOL;
    $this->content[3] = '/*  ================================================================  */';

    return $this;
  }

  private function s_post()
  {
    $this->storage['POST'] = $_POST;
    return $this;
  }

  private function s_session()
  {
    $this->storage['SESSION'] = $_SESSION;
    return $this;
  }

  private function s_cookie()
  {
    $this->storage['COOKIE'] = $_COOKIE;
    return $this;
  }

  private function s_get()
  {
    $this->storage['GET'] = $_GET;
    return $this;
  }

  private function s_input()
  {
    $input_raw = file_get_contents('php://input');

    $this->storage['INPUT'] = [];
    $this->storage['INPUT']['RAW'] = Assist::CleanEOL($input_raw);

    ($input_json = json_decode($this->storage['INPUT']['RAW'], true))
      && $this->storage['INPUT']['JSON'] = $input_json;

    return $this;
  }

  private function s_request()
  {
    $this->storage['REQUEST'] = $_REQUEST;
    return $this;
  }

  private function s_files()
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

  private function s_headers()
  {
    $this->storage['HEADERS'] = getallheaders();
    return $this;
  }

  private function s_server()
  {
    $this->storage['SERVER'] = $_SERVER;
    return $this;
  }
}
