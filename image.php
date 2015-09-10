<?php
$id=(int)$_GET['id'];
header('Content-type: image/png');
//v.jpg is actually a png file.
if(!file_exists('qqbot/'.$id.'/v.jpg')) $filename='img/noqrcode.png'; else $filename='qqbot/'.$id.'/v.jpg';
$image = @imagecreatefrompng($filename);
imagepng($image);
imageDestroy($image);
exit;
?>