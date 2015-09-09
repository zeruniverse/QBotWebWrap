<?php
$id=(int)$_POST['id'];
$results = shell_exec('tail -n 30 qqbot/'.$id.'/log.log');
echo $results;
?>