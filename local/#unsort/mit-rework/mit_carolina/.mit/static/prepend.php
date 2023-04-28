<?php

$_DEBUG = isset($_GET['dev']);

$GET = $_DEBUG ? (empty($_GET['dev']) ? 'dbg' : $_GET['dev']) : '';

switch ($GET) {
  case 'dbg':
    header('content-type: text/plain');
    break;
  case 'jsn':
    header('content-type: application/json');
    break;

  default:
    break;
}