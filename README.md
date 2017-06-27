QQ机器人WEB控制台
=========  
[![Build Status](https://travis-ci.org/zeruniverse/QBotWebWrap.svg)](https://travis-ci.org/zeruniverse/QBotWebWrap)
[![Codacy Badge](https://api.codacy.com/project/badge/grade/ad8d129910134c2aa5591a99587aeb7c)](https://www.codacy.com/app/zzy8200/QBotWebWrap)
![Environment](https://img.shields.io/badge/PHP-5.2+-blue.svg)
![Environment](https://img.shields.io/badge/python-2.7-blue.svg)
![Environment](https://img.shields.io/badge/MySQL-required-ff69b4.svg)
![License](https://img.shields.io/github/license/zeruniverse/QBotWebWrap.svg)      
**该项目为QQRobot, QQParking, QzoneLiker提供一个网页版控制台，降低了使用门槛。**
有关[QQRobot](https://github.com/zeruniverse/QQRobot), [QQParking](https://github.com/zeruniverse/QQParking), [QzoneLiker](https://github.com/zeruniverse/QzoneLiker) 请见原项目。
用户输入基本参数后点击运行，然后等待网页中出现二维码用手机QQ（或安全中心）扫描登陆即可。该项目随以上三个项目一起更新，请不要使用RELEASE，直接下载[MASTER分支](https://github.com/zeruniverse/QBotWebWrap/archive/master.zip)使用。

DEMO: [qqbot.jeffery.cc](http://qqbot.jeffery.cc) (仅供测试，自动定期杀死所有进程，限制10并发)  
  
This project is a Web-based console for [QQRobot](https://github.com/zeruniverse/QQRobot), [QQParking](https://github.com/zeruniverse/QQParking) and [QzoneLiker](https://github.com/zeruniverse/QzoneLiker).  
Users no longer need to type command in Terminal. They can now do it with a web browser!  
  
## 如何使用（服务器端配置）  
+ 确保安装有PHP 5.2+，MySQL与Python2.7(需要PyExecJS包)   
+ 新建一个文件夹并将此作为网站根目录，将[QBotWebWrap](https://github.com/zeruniverse/QBotWebWrap/archive/master.zip)解压至文件夹内。   
+ 新建一个数据库，导入[qwrap.sql](https://raw.githubusercontent.com/zeruniverse/QBotWebWrap/master/qwrap.sql)。    
+ 假设您刚刚建立的文件夹是qwrapweb. `cd qwrapweb`   
+ 将里面的qqbot文件夹权限改为777 `sudo chmod -R 777 qqbot`    
+ 将function/sqllink.php改为您自己的数据库用户名，数据库和密码。 `vim function/sqllink.php`  
  
您可以改变qqbot文件夹内各项目的qqbot.py将tulingkey, SMTP邮箱等参数改为自己的参数。避免公用资源被耗尽引起错误。具体方案请参考3个原项目。  

## 如何使用（客户端）

+ 打开浏览器，在主页选择需要启动的项目  
+ 输入参数（如有必要），点击 RUN XXX   
+ 浏览器稍候将自动跳转至状态页  
+ 等待状态页QR CODE 出现  
+ 用手机QQ/安全中心扫描QR CODE  
+ 等待QR CODE消失  
+ 记录SID并关闭页面（可用SID在首页重新进入状态页）  
+ 使用SID从首页进入状态页检查状态，下载log 或结束自己的BOT（杀死进程）   
  
## References  
+ [QQParking](https://github.com/zeruniverse/QQParking): 自动回复私聊，留言转发邮箱   
+ [QQRobot](https://github.com/zeruniverse/QQRobot): 群聊小黄鸡      
+ [QzoneLiker](https://github.com/zeruniverse/QzoneLiker): QQ空间自动点赞机   
  
## 可更改参数列表  
### 必须更改  
+ `function/sqllink.php` $dbname 数据库名  
+ `function/sqllink.php` $dbusr 数据库用户名  
+ `function/sqllink.php` $dbpwd 数据库密码  
  
### 建议更改参数  
+ `qqbot/(qqparking|qqrobot)/qqbot.py` tuling key （见references），smtp 服务器，用户名，密码  
+ `qqbot/qzoneliker/qqbot.py` smtp 服务器，用户名，密码  
  
### 可选更改参数  
+ `qqbot/(qqparking|qqrobot|qzoneliker)/qqbot.py` 二维码重新获取次数 （默认6次获取均未扫描则结束程序）  
+ `qqbot/qzoneliker/qqbot.py` QzoneLiker最多错误重试次数（默认5次）  
+ `create.php` $MAXPROCESS 最多并行线程数（即最多挂几个机器人，默认10）  
+ `sid`长度 （create.php `randomstr()`函数，默认6位大写字母，如大于10位请同时修改数据库SID的VARCHAR长度）  
+ `qqbot/(qqparking|qqrobot)/qqbot.py` 轮询最多失败次数限制，默认5次  
  
## 运行截图  
+ 输入参数页面（以QQRobot举例）  
<img width="541" alt="capture111" src="https://cloud.githubusercontent.com/assets/4648756/9830055/00ed249c-58d5-11e5-823d-9c6456f3112d.PNG">
  
+ 点击运行（绿色按钮）后页面 （二维码获取中）  
<img width="801" alt="capture222" src="https://cloud.githubusercontent.com/assets/4648756/9830059/24166ef6-58d5-11e5-8339-994e8e02aa6f.PNG">
  
+ 二维码成功获取后页面（获取二维码约需要5秒钟）  
<img width="754" alt="capture333" src="https://cloud.githubusercontent.com/assets/4648756/9830068/4be307dc-58d5-11e5-8f80-10f7a57c8423.PNG">
  
+ 扫描二维码并成功登陆后，二维码消失，此时可以关闭页面  
<img width="802" alt="capture444" src="https://cloud.githubusercontent.com/assets/4648756/9830073/6c35b0f2-58d5-11e5-8624-d539a7c1319d.PNG">
  
+ DOWNLOAD LOG可下载完整LOG  
<img width="789" alt="capture555" src="https://cloud.githubusercontent.com/assets/4648756/9830077/7d629ff2-58d5-11e5-9e1d-6b2fed912772.PNG">
  
## 可扩展性/二次开发  
`qqbot`文件夹内各项目请参考References  
`create.php`是主要程序，负责产生一个进程ID，将对应的项目文件夹（qqbot|qqparking|qzoneliker）拷贝到这个ID命名的文件夹内，然后把用户输入的参数写进配置文件。最后执行shell命令启动python脚本并获取PID存入数据库。create.php创建新进程前会检查数据库内所有PID，并删除已经停止运行的PID，同时删除对应文件夹。如果删除后数据库内进程数量大于$MAXPROCESS,则拒绝添加线程请求  
`log.php` & `logdownload.php` 根据id找到qqbot/$id/log.log，并输出  
`image.php`根据id找到qqbot/$id/v.png，并输出  
`killit.php`杀死sid对应的进程  
数据库中process表存有`id`,`pid`,`sid`。其中`pid`是对应BOT的进程号，`id`对应的是进程编号（对应文件夹名），`sid`是与前端交互的唯一标识码，用于解决`id`容易猜测的问题。用户不会得到`id`，只会得到`sid`。   
  
### 举例：添加用户登录功能，限制每个用户最多只能开两个BOT  
+ 数据库中加入user表，做好注册登陆模块  
+ process表中加入user字段，将每个process对应到user  
+ `create.php`检查该user是否超过限制，如果不是则创建进程时将user id同时写入process表对应的行  
+ `status.php killit.php image.php log.php logdownload.php`检查SID同时必须检查该SID对应的行的user id与session里的user id是否一致  
+ `index.html`改为`index.php`，输出数据库中该USER已经执行的BOT状态链接  
