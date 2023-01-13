<?php

include '../src/ColorImage.php';
include '../src/InfoColorImage.php';

$resoruce = imagecreatefromjpeg('demo.jpg');

$info = \Ast\ColorImage\ColorImage::getInfo($resoruce,true);

echo $info;