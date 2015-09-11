# -*- coding: utf-8 -*-

import re
import random
import json
import os
import sys
import datetime
import time
import threading
import logging
import urllib
from HttpClient import HttpClient

reload(sys)
sys.setdefaultencoding("utf-8")

# CONFIGURATION FIELD
checkFrequency = 180
#check every k seconds
# STOP EDITING HERE
HttpClient_Ist = HttpClient()
UIN = 0
skey = ''
Referer = 'http://user.qzone.qq.com/'
QzoneLoginUrl = 'http://xui.ptlogin2.qq.com/cgi-bin/xlogin?proxy_url=http%3A//qzs.qq.com/qzone/v6/portal/proxy.html&daid=5&pt_qzone_sig=1&hide_title_bar=1&low_login=0&qlogin_auto_login=1&no_verifyimg=1&link_target=blank&appid=549000912&style=22&target=self&s_url=http%3A%2F%2Fqzs.qq.com%2Fqzone%2Fv5%2Floginsucc.html%3Fpara%3Dizone&pt_qr_app=%E6%89%8B%E6%9C%BAQQ%E7%A9%BA%E9%97%B4&pt_qr_link=http%3A//z.qzone.com/download.html&self_regurl=http%3A//qzs.qq.com/qzone/v6/reg/index.html&pt_qr_help_link=http%3A//z.qzone.com/download.html'

initTime = time.time()


logging.basicConfig(filename='log.log', level=logging.DEBUG, format='%(asctime)s  %(filename)s[line:%(lineno)d] %(levelname)s %(message)s', datefmt='%a, %d %b %Y %H:%M:%S')

def getAbstime():
    return int(time.time())
    
def date_to_millis(d):
    return int(time.mktime(d.timetuple())) * 1000

def getReValue(html, rex, er, ex):
    v = re.search(rex, html)

    if v is None:
        logging.error(er)

        if ex:
            raise Exception, er
        return ''

    return v.group(1)
    
# -----------------
# 登陆
# -----------------
class Login(HttpClient):
    MaxTryTime = 5

    def __init__(self, vpath, qq=0):
        global UIN, Referer, skey
      self.VPath = vpath  # QRCode保存路径
        AdminQQ = int(qq)
        logging.critical("正在获取登陆页面")
        self.setCookie('_qz_referrer','qzone.qq.com','qq.com')
        self.Get(QzoneLoginUrl,'http://qzone.qq.com/')
        StarTime = date_to_millis(datetime.datetime.utcnow())
        T = 0
        while True:
            T = T + 1
            self.Download('http://ptlogin2.qq.com/ptqrshow?appid=549000912&e=2&l=M&s=3&d=72&v=4&daid=5', self.VPath)
            LoginSig = self.getCookie('pt_login_sig')
            logging.info('[{0}] Get QRCode Picture Success.'.format(T))  
            logging.info('请用手机QQ/安全中心扫描二维码登陆')
            while True:
                html = self.Get('http://ptlogin2.qq.com/ptqrlogin?u1=http%3A%2F%2Fqzs.qq.com%2Fqzone%2Fv5%2Floginsucc.html%3Fpara%3Dizone&ptredirect=0&h=1&t=1&g=1&from_ui=1&ptlang=2052&action=0-0-{0}&js_ver=10131&js_type=1&login_sig={1}&pt_uistyle=32&aid=549000912&daid=5&pt_qzone_sig=1'.format(date_to_millis(datetime.datetime.utcnow()) - StarTime, LoginSig), QzoneLoginUrl)
                # logging.info(html)
                ret = html.split("'")
                if ret[1] == '65' or ret[1] == '0':  # 65: QRCode 失效, 0: 验证成功, 66: 未失效, 67: 验证中
                    break
                time.sleep(2)
            if ret[1] == '0' or T > self.MaxTryTime:
                break

        if ret[1] != '0':
            raise ValueError, "RetCode = "+ret['retcode']
            return
        logging.critical("二维码已扫描，正在登陆")
        
        # 删除QRCode文件
        if os.path.exists(self.VPath):
            os.remove(self.VPath)

        # 记录登陆账号的昵称
        tmpUserName = ret[11]
        
        self.Get(ret[5])
        UIN = getReValue(ret[5], r'uin=([0-9]+?)&', 'Fail to get QQ number', 1)
        Referer = Referer+str(UIN)
        skey = self.getCookie('skey')
        logging.info('登陆成功，用户名：'+tmpUserName)
# -----------------
# 计算g_tk
# -----------------  
def utf8_unicode(c):            
    if len(c)==1:                                 
        return ord(c)
    elif len(c)==2:
        n = (ord(c[0]) & 0x3f) << 6              
        n += ord(c[1]) & 0x3f              
        return n        
    elif len(c)==3:
        n = (ord(c[0]) & 0x1f) << 12
        n += (ord(c[1]) & 0x3f) << 6
        n += ord(c[2]) & 0x3f
        return n
    else:                
        n = (ord(c[0]) & 0x0f) << 18
        n += (ord(c[1]) & 0x3f) << 12
        n += (ord(c[2]) & 0x3f) << 6
        n += ord(c[3]) & 0x3f
        return n

def getGTK(skey):
    hash = 5381
    for i in range(0,len(skey)):
        hash += (hash << 5) + utf8_unicode(skey[i])
    return hash & 0x7fffffff
# -----------------
# LIKE
# ----------------- 
def like(unikey,curkey,dataid,time):
    reqURL = 'http://w.qzone.qq.com/cgi-bin/likes/internal_dolike_app?g_tk='+str(getGTK(skey))
    data = (
            ('qzreferrer', Referer),
            ('opuin', UIN),
            ('unikey', str(unikey)),
            ('curkey', str(curkey)),
            ('from', '1'),
            ('appid', '311'),
            ('typeid', '0'),
            ('abstime', str(time)),
            ('fid', str(dataid)),
            ('active', '0'),
            ('fupdate', '1')
        )
    rsp = HttpClient_Ist.Post(reqURL, data, Referer)
    getReValue(rsp, r'"code":(0)', 'Fail to like unikey='+str(unikey)+';curkey='+str(curkey)+';fid='+str(dataid), 0)

# -----------------
# 主函数
# ----------------- 
def MsgHandler():
    html=HttpClient_Ist.Get(Referer,Referer)
    fkey=re.findall(r'<div class="f-item f-s-i" id=".*?" data-feedsflag=.*?" data-iswupfeed=".*?" data-key="(.*?)" data-specialtype=.*?" data-extend-info=".*?">',html)
    if fkey is None:
        raise Exception, 'Fail to find any feeds'
    split_string=re.split(r'<div class="f-item f-s-i" id=".*?" data-feedsflag=.*?" data-iswupfeed=".*?" data-key=".*?" data-specialtype=.*?" data-extend-info=".*?">',html)
    for i in range (0,len(fkey)):
        try:
            btn_string = re.search(r'<a class="item qz_like_btn_v3" data-islike="0" data-likecnt=".*?" data-showcount=".*?" data-unikey="(.*?)" data-curkey="(.*?)" data-clicklog="like" href="javascript:;">', split_string[i+1])
            if btn_string is None:
                continue
            abstime = re.search(r'data-abstime="(\d*?)"',split_string[i+1])
            if abstime is None:
                continue
            like(btn_string.group(1),btn_string.group(2),fkey[i],abstime.group(1))
            logging.info('已点赞'+btn_string.group(2))
        except Exception, e:
            logging.error(str(e))

# -----------------
# 主程序
# -----------------

if __name__ == "__main__":
    vpath = './v.png'
    qq = 0
    if len(sys.argv) > 1:
        vpath = sys.argv[1]
    if len(sys.argv) > 2:
        qq = sys.argv[2]

    try:
        qqLogin = Login(vpath, qq)
    except Exception, e:
        logging.critical(str(e))
        os._exit()
    errtime=0
    while True:
        try:
            if errtime > 5:
                break
            MsgHandler()
            time.sleep(checkFrequency)
            errtime = 0
        except Exception, e:
            logging.error(str(e))
            errtime = errtime + 1
