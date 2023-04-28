<?php

session_start();

define('MIT_PATH', __DIR__);

require_once __DIR__ . '/static/assist.php';
require_once __DIR__ . '/static/trapper.php';

$Trapper = (new Trapper)
  ->catchRequest();
