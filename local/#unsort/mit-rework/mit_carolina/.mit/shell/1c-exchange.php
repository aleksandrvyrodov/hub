#!/usr/bin/php -f
<?php
rename(__DIR__ . '/1c-exchange.php', __DIR__ . '/1c-exchange.php.lock');

function save_log($c, $time = 'S')
{
  $content[0] = PHP_EOL . PHP_EOL;
  $content[1] = '$DT___' . date('y_m_d__H_i_s') . "___{$time}" . ' = ';
  $content[2] = ';' . PHP_EOL . PHP_EOL;
  $content[3] = '/*  ================================================================  */';

  $content[1] .= var_export($c, true);
  $content = implode('', $content);

  file_put_contents(
    __DIR__ . '/../.term/1c-log.php',
    $content,
    FILE_APPEND
  );
}

$pid = (int)getmypid();


save_log(['PID' => $pid]);

$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../');

ob_start();
include_once __DIR__  . '/../static/1c-exchange.php';

$mes = ob_get_contents();
ob_end_clean();

save_log([
  'PID' => $pid,
  'OUT' => explode(PHP_EOL, trim($mes))
], 'F');


rename(__DIR__ . '/1c-exchange.php.lock', __DIR__ . '/1c-exchange.php');
