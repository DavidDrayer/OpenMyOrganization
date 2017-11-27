<?php
header ("Content-type: image/png");
$im = @imagecreate (250, 50)
   or die ("Impossible d'initialiser la bibliothèque GD");
$background_color = imagecolorallocate ($im, 255, 255, 200);
$text_color = imagecolorallocate ($im, 233, 14, 91);
imagestring ($im, 3, 10, 10,  "Module : ".$_GET["name"], $text_color); 
imagepng ($im);
imagedestroy($im);
?> 