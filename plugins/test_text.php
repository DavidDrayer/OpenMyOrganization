<?php
include_once 'Text/Diff.php';
include_once 'Text/Diff/Renderer/inline.php';

$lines1 = array(
    "Ceci est ma première ligne rouge"
);
$lines2 = array(
    "Ceci est ma seconde ligne rouge.\n"    
);

$diff = new Text_Diff("auto", array($lines1, $lines2));

$renderer = new Text_Diff_Renderer_inline();
echo $renderer->render($diff);
?>