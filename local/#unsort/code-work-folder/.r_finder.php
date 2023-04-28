<?php

header("Content-Type: text/plain");

// exec('find '.__DIR__.' -type f -newermt 2023-03-03T10:00 ! -newermt 2023-03-03T12:00', $out, $res);
exec('find ' . __DIR__ . ' -type f -perm 444 ', $out, $res);

var_dump($out);
var_dump($res);

if (isset($_GET['del'])) {
  foreach ($out as $file) {
    exec('rm -f ' . $file, $out, $res);
  }

  var_dump($out);
  var_dump($res);
}
