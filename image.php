<?php
require_once('function/sqllink.php');
if(!isset($_GET['id'])) die('CAN NOT FIND ID IN THE PARAMETER');
$link=sqllink();
if(!$link) die('DATABASE ERROR');
$res=sqlexec('SELECT * FROM `process` where `sid`=?',array($_GET['id']),$link);
$result=$res->fetch(PDO::FETCH_ASSOC);
if ($result==FALSE)  die('THIS PROCESS DOES NOT EXISTS OR ALREADY TERMINATED AND REMOVED FROM THE SERVER!');
header('Content-type: image/png');
//v.jpg is actually a png file.
if(!file_exists('qqbot/'.$result['id'].'/v.png')) $filename='img/noqrcode.png'; else $filename='qqbot/'.$result['id'].'/v.png';
$image = @imagecreatefrompng($filename);
imagepng($image);
imageDestroy($image);
exit;
?>