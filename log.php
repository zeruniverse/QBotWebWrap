<?php
require_once('function/sqllink.php');
if(!isset($_POST['id'])) die('CAN NOT FIND ID IN THE PARAMETER');
$link=sqllink();
if(!$link) die('DATABASE ERROR');
$res=sqlexec('SELECT * FROM `process` where `sid`=?',array($_POST['id']),$link);
$result=$res->fetch(PDO::FETCH_ASSOC);
if ($result==FALSE)  die('THIS PROCESS DOES NOT EXISTS OR ALREADY TERMINATED AND REMOVED FROM THE SERVER!');
$results = '';
if(file_exists('qqbot/'.$result['id'].'/log.log')) $results = shell_exec('tail -n 30 qqbot/'.$result['id'].'/log.log');
echo $results;
?>