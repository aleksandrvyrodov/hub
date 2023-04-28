<?php

function SandBoxVars($filename)
{
  switch ($filename) {
    case '1c-log':
      break;
    default:
      $filename = 'caught';
      break;
  }

  if(isset($_GET['clean']))
    file_put_contents(__DIR__ . "/../.term/$filename.php", <<<CONTENT
      <?php


      /*  ================================================================  */
      CONTENT);


  require __DIR__ . "/../.term/$filename.php";

  unset($filename);

  $list_DT = get_defined_vars();

  return array_reverse($list_DT);
}

header('Content-type: application/json');
echo json_encode(SandBoxVars($_GET['view'] ?? ''));
