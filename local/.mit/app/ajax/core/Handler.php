<?php

namespace MIT\App\Ajax\Core;

use ReflectionClass;

class Handler
{
  private static Handler $Initial;
  private array $content_type = [];

  private array $data_request;

  private $reason;
  private $action;

  static public function Init(): Handler
  {
    if (!isset(self::$Initial))
      self::$Initial = new static();

    return self::$Initial;
  }

  static public function errorAnswerPack(\Throwable $Exception)
  {
    $mes = $Exception->getMessage();

    return (new AnswerPack)
      ->setProp(AnswerPack::STATUS, 'ERR')
      ->setProp(AnswerPack::MESSAGE, $mes);
  }

  static public function successAnswerPack($data)
  {
    return (new AnswerPack(true))
      ->setProp(AnswerPack::STATUS, 'OK')
      ->setProp(AnswerPack::DATA, $data);
  }

  # -------------------------

  private function __construct()
  {
  }

  #region chech ready work and set content type
  private function setContentType(): Handler
  {
    if (isset($_SERVER["CONTENT_TYPE"]) && preg_match('~(?:([^;\s]+)/([^;\s]+))~', $_SERVER["CONTENT_TYPE"], $match))
      $this->content_type = [
        'type' => $match[1],
        'subtype' => $match[2],
      ];
    return $this;
  }

  private function contentType($full = false)
  {
    if (empty($content_type))
      return null;

    if ($full)
      return implode('/', $this->content_type);
    else
      return $this->content_type;
  }
  #endregion

  #regin grab and set date
  private function grabDateRequest(): Handler
  {
    $this->data_request = $_GET;
    $this->data_request = array_merge($this->data_request, $_POST);

    // if ($this->contentType(true) == 'application/json')
    $this->data_request = array_merge(
      $this->data_request,
      (array)json_decode(
        file_get_contents('php://input'),
        true
      )
    );

    if (empty($this->data_request))
      throw new \Exception("Empty request", 1);

    if (!isset($this->data_request['burn']))
      throw new \Exception("Incomplete data", 1);

    // FAR
    // сделать спл ключ ризон, сохраняет в себе все выполненные данные

    return $this->setRoadMap();
  }

  private function setRoadMap(): Handler
  {
    $burn = $this->data_request['burn'];

    [
      $this->reason,
      $this->action,
    ] = explode('.', $burn);

    if (empty($this->reason))
      throw new \Exception("Empty reason", 1);

    if (empty($this->action))
      throw new \Exception("Empty action", 1);

    unset($this->data_request['burn']);

    return $this;
  }
  #endregion;

  public function execBurn()
  {
    $this
      ->setContentType()
      ->grabDateRequest();

    $reason = 'JR\\Ajax\\Reason\\' . ucfirst($this->reason);

    if (!class_exists($reason))
      throw new \Exception("Undefined reason", 1);

    $RefReason = new ReflectionClass($reason);

    if (!$RefReason->hasMethod($this->action))
      throw new \Exception("Undefined action", 1);

    try {
      $Reason = $RefReason->newInstance($this->data_request);
      $RefAction = $RefReason->getMethod($this->action);
      $result = $RefAction->invoke($Reason);

      return self::successAnswerPack($result);
    } catch (\Throwable $th) {
      throw new \Exception($th->getMessage(), 1, $th);
    }
  }
}
