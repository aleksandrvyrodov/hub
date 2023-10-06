#!/usr/bin/php -f
<?php

/*
add file [1c-exchange.php]
file content ▽▽▽:

#!/usr/bin/php -f
<?php

define('MIT__FILE__', __FILE__);

require_once __DIR__ . '/1c-exchange.core.php';
*/

use const MIT\Shell\EX1C_FILE_ORIG;
use const MIT\Shell\EX1C_MAX_RELOAD;

require_once __DIR__ . '/../.defined.php';

!defined('MIT__FILE__')
  && define('MIT__FILE__', __FILE__);

function save_log($c, $time = 'S')
{
  $content[0] = PHP_EOL . PHP_EOL;
  $content[1] = '$DT___' . date('y_m_d__H_i_s') . '___' . hrtime(true) . "___{$time}" . ' = ';
  $content[2] = ';' . PHP_EOL . PHP_EOL;
  $content[3] = '/*  ================================================================  */';

  $content[1] .= var_export($c, true);
  $content = implode('', $content);

  file_put_contents(
    __DIR__ . '/../.term/1c-log.php',
    $content,
    FILE_APPEND
  );
};

function ReStart($mes)
{
  global $PID, $Named;

  save_log([
    'PID' => $PID,
    'OUT' => explode(PHP_EOL, trim($mes))
  ], 'Fc');

  // sleep(5);

  $FILE_NEXT = __DIR__ . '/' . $Named->Locked();

  // $JACK = "nohup php -f $FILE_NEXT > /dev/null 2>&1 &";

  if (rename(
    __DIR__ . '/' . $Named->Current(),
    $FILE_NEXT
  )) {

    `nohup php -f $FILE_NEXT > /dev/null 2>&1 &`;
    `kill SIGKILL $PID`;

    exit();
  } else
    throw new Exception("Failed restart", 1);
};

define('MIT\\Shell\\LAST_MOD_TIME', stat(MIT__FILE__)['ctime']);

global $Named;
$Named = new class()
{
  static $EXT;
  static $First;

  public function __construct(
    private $filename =  EX1C_FILE_ORIG,
    private $locker =  '.lock',
    private $separator =  '-',
    private $time = 0
  ) {
    static::$EXT = strtolower(pathinfo(MIT__FILE__, PATHINFO_EXTENSION));
    if ((static::$First = static::$EXT === 'php'))
      $this->time = -1;
    else
      $this->time = (int)explode('-', static::$EXT)[1];
  }

  public function __set($name, $value)
  {
  }

  public function Current(): string
  {
    if (static::$EXT === 'php')
      return $this->filename;
    else
      return implode('', get_object_vars($this));
  }

  public function Locked(): string
  {
    $_ = get_object_vars($this);
    $_['time']++;
    return implode('', $_);
  }

  public function Final(): string
  {
    return (static::$First ? $this->Locked() : $this->Current());
  }

  public function getTime(): int
  {
    return ($this->time + 1);
  }
};


$Named::$First
  && rename(
    __DIR__ . '/' . $Named->Current(),
    __DIR__ . '/' . $Named->Locked()
  );


global $PID;
$PID = (int)getmyPID();

save_log(['PID' => $PID], $Named::$First ? 'S' : 'Sc');

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../');

ob_start();
try {
  include_once __DIR__  . '/../static/' . EX1C_FILE_ORIG;
} catch (\Throwable $th) {
  if ($th->getCode() == -2) {
    if ($Named->getTime() < EX1C_MAX_RELOAD)
      ReStart(
        ''
          . 'reject' . PHP_EOL
          . $th->getMessage() . PHP_EOL
      );
    else
      echo ''
        . 'error' . PHP_EOL
        . 'exceeded limit of automatic restart' . PHP_EOL
        . $th->__toString() . PHP_EOL;
  } else {
    echo ''
      . 'error' . PHP_EOL
      . $th->getMessage() . PHP_EOL
      . $th->__toString() . PHP_EOL;
  }
}

$mes = ob_get_contents();
ob_end_clean();

save_log([
  'PID' => $PID,
  'OUT' => explode(PHP_EOL, trim($mes))
], 'F');


rename(
  __DIR__ . '/' . $Named->Final(),
  __DIR__ . '/' . EX1C_FILE_ORIG
);
