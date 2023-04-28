<?php

class Assist
{
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
            $files[$i+$gi][$k] = $v;
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
