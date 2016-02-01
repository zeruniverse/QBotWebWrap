<?php
$MAXPROCESS=10;
function randomstr(){
    $code = '';
    for($i=1;$i<=6;$i++)
	{
		$randchar=rand(0,25);
		$code=$code.chr($randchar+ord("A"));
	}
    return $code;
}

function deldir($dir){
  $mydir=opendir($dir);
  while ($file=readdir($mydir)) {
    if($file!="." && $file!="..") {
      $fullpath=$dir."/".$file;
      if(!is_dir($fullpath)) {
          unlink($fullpath);
      } else {
          deldir($fullpath);
      }
    }
  }

  closedir($mydir);

  if(rmdir($dir)) {
    return true;
  }
  
  return false;

}

function pstatus($pid){
    $command = 'ps -p '.$pid;
    exec($command,$returnval);
    if (!isset($returnval[1])) return false;
    else return true;
}

function dcopy($source, $destination){   
	if (!is_dir($source)){
		echo "ERROR: THE".$source." is not a directory!";
		return 0;
	}
	if (!is_dir($destination)){
		mkdir($destination);
	} else return 0;
	$handle= dir($source);
 	while($entry=$handle->read()){
		if(($entry!='.')&&($entry!='..')){
			if(is_dir($source."/".$entry)){
				dcopy($source."/".$entry,$destination."/".$entry); 
			}
			else{
				copy($source."/".$entry,$destination."/".$entry); 
			}
		}
	}
	return 1;
}
function delete_old_process($link){
    $ret=sqlquery('SELECT * FROM `process` where 1',$link);
    while ($i = $ret->fetch(PDO::FETCH_ASSOC)){
    if (!pstatus($i['pid'])){
        sqlexec('DELETE FROM `process` where pid=?',array($i['pid']),$link);
        deldir('qqbot/'.$i['id']);
    }
    }
}
require_once('function/sqllink.php');
if(!isset($_POST['type'])) die('{"retcode":999,"msg":"INCOMPLETE POST DATA"}');
$link=sqllink();
if(!$link) die('{"retcode":999,"reason":"DATABASE ERROR"}');
delete_old_process($link);
$res=sqlquery('SELECT count(*) FROM `process`',$link);
$result=$res->fetch(PDO::FETCH_NUM);
if((int)($result[0])>$MAXPROCESS) die('{"retcode":9,"msg":"TOO MANY simultaneous process. Try again later!"}');;

if(!$link->beginTransaction()) die('{"retcode":999,"reason":"DATABASE ERROR"}');
$res=sqlquery('SELECT max(`id`) FROM `process`',$link);
$result=$res->fetch(PDO::FETCH_NUM);
$maxnum=($result==FALSE)?0:(int)($result[0]);
$newid=$maxnum+1;

switch ($_POST['type']) {
    case 'qzoneliker':
        if (dcopy('qqbot/qzoneliker', 'qqbot/'.$newid)==0) {$link->rollBack(); die('{"retcode":999,"msg":"UNABLE TO CREATE FOLDER!"}');}
        $myfile = fopen('qqbot/'.$newid."/email.txt", "w");
		if(!$myfile) {$link->rollBack(); deldir('qqbot/'.$newid); die('{"retcode":996,"msg":"UNABLE TO CREATE GROUPFOLLOW.TXT!"}');}
        fwrite($myfile, $_POST['email']);
        fclose($myfile);
        break;
    case 'qqrobot':
        if (dcopy('qqbot/qqrobot', 'qqbot/'.$newid)==0) {$link->rollBack(); die('{"retcode":999,"msg":"UNABLE TO CREATE FOLDER!"}');}
        $myfile = fopen('qqbot/'.$newid."/groupfollow.txt", "w");
		if(!$myfile) {$link->rollBack(); deldir('qqbot/'.$newid); die('{"retcode":996,"msg":"UNABLE TO CREATE GROUPFOLLOW.TXT!"}');}
        fwrite($myfile, $_POST['groups']);
        fclose($myfile);
        $myfile = fopen('qqbot/'.$newid."/email.txt", "w");
		if(!$myfile) {$link->rollBack(); deldir('qqbot/'.$newid); die('{"retcode":996,"msg":"UNABLE TO CREATE GROUPFOLLOW.TXT!"}');}
        fwrite($myfile, $_POST['email']);
        fclose($myfile);
        break;
    case 'qqparking':
        if (dcopy('qqbot/qqparking', 'qqbot/'.$newid)==0) {$link->rollBack(); die('{"retcode":999,"msg":"UNABLE TO CREATE FOLDER!"}');}
        $myfile = fopen('qqbot/'.$newid."/config.txt", "w");
		if(!$myfile)  {$link->rollBack(); deldir('qqbot/'.$newid); die('{"retcode":996,"msg":"UNABLE TO CREATE GROUPFOLLOW.TXT!"}');}
        fwrite($myfile, $_POST['email']."\n");
        fwrite($myfile, $_POST['welcome']."\n");
        fclose($myfile);
        break;
    default:
        $link->rollBack();
        die('{"retcode":996,"msg":"UNKNOWN TYPE SUBMITTED!"}');
        break;
}
shell_exec('chmod -R +x qqbot/'.$newid);
shell_exec('chmod -R +w qqbot/'.$newid);
$command = 'cd qqbot/'.$newid.'; nohup python2 qqbot.py > /dev/null 2>&1 & echo $!';
exec($command ,$op);
$pid = (int)$op[0];

while (true){
	$sid=randomstr();
	$sql="SELECT COUNT(*) FROM `process`  WHERE `sid`=?";
	$res=sqlexec($sql,array($sid),$link);
    $num= $res->fetch(PDO::FETCH_NUM);
    $num=$num[0];
	if($num==0) break;
}
sqlexec('INSERT INTO `process` VALUES (?,?,?)',array($newid,$pid,$sid),$link);
$link->commit();
die('{"retcode":0,"reason":"SUCCESS","id":"'.$sid.'"}');
?>
