<?php
$id=(int)$_POST['id'];
$results = '';
if(file_exists('qqbot/'.$id.'/log.log')) $results = shell_exec('tail -n 30 qqbot/'.$id.'/log.log');
echo $results;
?>