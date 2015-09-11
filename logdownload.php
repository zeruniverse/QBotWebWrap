<?php
require_once('function/sqllink.php');
if(!isset($_GET['id'])) die('CAN NOT FIND ID IN THE PARAMETER');
$link=sqllink();
if(!$link) die('DATABASE ERROR');
$res=sqlexec('SELECT * FROM `process` where `sid`=?',array($_GET['id']),$link);
$result=$res->fetch(PDO::FETCH_ASSOC);
if ($result==FALSE)  die('THIS PROCESS DOES NOT EXISTS OR ALREADY TERMINATED AND REMOVED FROM THE SERVER!');
if(!file_exists('qqbot/'.$result['id'].'/log.log')) die('No Such File');
header('Content-Type: text/plain');
header('Content-Disposition: inline; filename="Log.txt"');
if(file_exists('qqbot/'.$result['id'].'/log.log')) readfile('qqbot/'.$result['id'].'/log.log'); else die('No Such File');
exit;
?>