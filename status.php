<?php
require_once('function/sqllink.php');
if(!isset($_GET['id'])) die('CAN NOT FIND ID IN THE PARAMETER');
$link=sqllink();
if(!$link) die('DATABASE ERROR');
$res=sqlexec('SELECT * FROM `process` where `sid`=?',array($_GET['id']),$link);
$result=$res->fetch(PDO::FETCH_ASSOC);
if ($result==FALSE)  die('THIS PROCESS DOES NOT EXISTS OR ALREADY TERMINATED AND REMOVED FROM THE SERVER!');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="QBot Web Wrap">
    <meta name="author" content="Jeffery">
    <link rel="icon" href="img/favicon.jpg">
  <title>QBOT</title>
  
  <meta name="author" content="Jeffery">
    <link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

  <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>

  <![endif]-->

  <!-- Fav and touch icons -->
  <link rel="shortcut icon" href="img/favicon.jpg">
  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/bootstrap.min.js"></script>
<style>
.nodeco
{text-decoration:none; color:#666666;}
.nodeco:hover,active,link,visited
{text-decoration:none; color:#666666;}
</style>
</head>

<body style="color:#666666">
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
              <div class="container">
				<div class="navbar-header">
					 <button type="button" class="navbar-toggle"  data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"> <span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button> <a class="navbar-brand" style="padding-top:3px;padding-bottom:3px;padding-right:55px" href="javascript: window.location.href='/'"><img height="43px" src="img/favicon.jpg" /></a>
				</div>
				
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li class="active">
							<a href="./index.html">Status</a>
						</li>
						<li>
							<a href="./qqparking.html">QQParking</a>
						</li>
						<li>
							<a href="./qqrobot.html">QQRobot</a>
						</li>
                        <li>
							<a href="./qzoneliker.html">QzoneLiker</a>
						</li>
						
        
                </ul>
				</div>
			  </div>	
</nav>
<div class="container theme-showcase">
      <div class="page-header">
        <h1>INSTRUCTIONS</h1>
	  </div>
      <div>
<p>1. Stay on this page until the QR Code is ready.</p>
<p>2. When QR Code is ready, you will see it in the QR Code part.</p>
<p>3. Scan QR Code with your mobile QQ or QQ security center.</p>
<p>4. Confirm that you successfully Login in Log.</p>
<p>5. Close this page and have a coffee!</p>
<p><br /></p>
<p>Please keep <span style="color:red">your session ID (SID):</span> <span style="color:blue"><?php echo $result['sid']; ?></span>. You can go back to check the log or download the log file.</p>
<p>The QR Code and Log refreshes every 5 seconds.</p>
<p>The last few lines of log will be on the screen. If you want all log, please download the log (at the bottom of this page). </p>
<p><br /></p>
<p>请等待二维码生成，当二维码生成后，下面的QR CODE部分会自动显示生成的二维码，在这以前请不要离开此页！</p>
<p>用手机QQ/安全中心扫描二维码，成功登陆后二维码会消失。二维码消失后即可关闭此页！</p>
<p>如果您需要返回检查LOG，请记住本次挂机的SID： <span style="color:blue"><?php echo $result['sid']; ?></span> (6位大写字母)，在首页输入这个SID可以返回本页！</p>
</div>

      <div class="page-header">
        <h1>QR CODE</h1>
	  </div>
      <div style="text-align: center"><img id="qrcode" /></div>
      <div class="page-header">
        <h1>LOG</h1>
	  </div>
      <div id="log" class="jumbotron" style="overflow:hidden;">

      </div>
      <div style="text-align:right;"><a href="logdownload.php?id=<?php echo $result['sid'];?>">DOWNLOAD LOG</a></div>
</div>
<script>
function replc(strmsg)
{
	strmsg=strmsg.split("\r").join("");
	strmsg=strmsg.split('&').join('&amp;');
	strmsg=strmsg.split('<').join('&lt;');
	strmsg=strmsg.split('>').join('&gt;');
	strmsg=strmsg.split(' ').join('&nbsp;');
	strmsg=strmsg.split("\n").join('<br />');
	return strmsg;
}
function refreshs()
{
    $("#qrcode").attr("src",'image.php?id=<?php echo $result['sid']?>&p=' + Math.random() + '.png');
    $.post('log.php',{id:'<?php echo $result['sid']?>'},function (msg)
    {
        $('#log').html(replc(msg));
    }
    );
}
refreshs();
timer = setInterval(function(){
		refreshs();
	},5000);
</script>
<footer class="footer ">
      <p>&copy; Jeffery</p>
</footer>
</body>
</html>
