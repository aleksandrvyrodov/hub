<?php

#...

elseif($this->format == File\Image::FORMAT_GIF)
{
  //save transparency
  $transparentColor = imagecolortransparent($this->resource);
  $totalColorSize = imagecolorstotal($this->resource);
  if($transparentColor >= 0 && $transparentColor < $totalColorSize)
  {
    $rgb = imagecolorsforindex($this->resource, $transparentColor);
    $transparentColor = imagecolorallocatealpha($picture, $rgb["red"], $rgb["green"], $rgb["blue"], 127);
    imagefilledrectangle($picture, 0, 0, $destinationWidth, $destinationHeight, $transparentColor);
  }
}

/* ---- log ---\/
2023-06-06 14:29:09 - Host: ####:443 - UNCAUGHT_EXCEPTION - [ValueError]
imagecolorsforindex(): Argument #2 ($color) is out of range (0)
/####/bitrix/modules/main/lib/File/Image/Gd.php:153

---- fix ---- \/
+++ 151: $totalColorSize = imagecolorstotal($this->resource);
--- 152: if($transparentColor >= 0)
+++ 152: if($transparentColor >= 0 && $transparentColor < $totalColorSize)
*/

#...