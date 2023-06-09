<?php

function out_split(?Closure $fn_out = null, mixed $data_out = null, bool $return = false){
  static $i = 0;
  static $data = [];

  if($return)
    return $data;

  ob_start();

  $fn_out($data_out);

  $data[$i++] = ob_get_contents();
  ob_end_clean();
}

