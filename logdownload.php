<?php
$id=(int)$_GET['id'];
if(!file_exists('qqbot/'.$id.'/log.log')) die('No Such File');
header('Content-Type: text/plain');
header('Content-Disposition: inline; filename="Log.txt"');
readfile('qqbot/'.$id.'/log.log');
exit;
?>