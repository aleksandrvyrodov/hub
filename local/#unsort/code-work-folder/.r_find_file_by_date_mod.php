<?php

header("Content-Type: text/plain");

exec('find '.__DIR__.' -type f -newermt 2022-06-29 ! -newermt 2022-07-01', $out, $res);

var_dump($out);
var_dump($res);
