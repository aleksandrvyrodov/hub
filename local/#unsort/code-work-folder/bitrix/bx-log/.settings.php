<?php
[
'exception_handling' =>
  array(
    'value' =>
    array(
      'debug' => true,
      'handled_errors_types' => E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED, //4437,
      'exception_errors_types' => E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED, //4437,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => array(
        'settings' => array(
          'file' => '/home/bitrix/logs/error_bx.log',
          'log_size' => 1000000,
        ),
      ),
    ),
    'readonly' => false,
  ),
];