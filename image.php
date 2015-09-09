<?php
$id=(int)$_GET['id'];
header('Content-type: image/jpeg');
if(!file_exists('qqbot/'.$id.'/v.jpg')) $filename='img/noqrcode.jpg'; else $filename='qqbot/'.$id.'/v.jpg';

$image = imagecreatefromjpeg($filename);
imagejpeg($image,'',100);
imageDestroy($image);
exit;
?>