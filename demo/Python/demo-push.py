#!/usr/bin/env python
# -*-coding:utf-8-*-
"""
Demo Push
HTTP-API Python SDK(Python2、python3)
 * Created by PyCharm.
 * User: yugao
 * version 2.2.2
 * Note: HTTPSDK for Python(适用于版本2.2.2插件):用于解析插件消息、构造返回数据，以及HTTP推送（发起HTTP请求）
 * Contact: 开发者邮箱 admin@ksust.com
 * 安装：pip install http-api-sdk
"""

from httpapi.HTTPSDK import *

if sys.version_info.major == 2:
    reload(sys)  # python2请配置相应编码
    sys.setdefaultencoding('utf8')
    sys.setdefaultencoding('gb18030')

push = HTTPSDK.httpPush("http://127.0.0.1:8080")
print(push.getGroupList())
print(push.sendPrivdteMsg('QQ', '你好'))

forward = HTTPSDK.msgForwardPush('QQ', '授权码')
print(forward.getGroupList())
print(forward.getLoginQQ())
print(forward.sendPrivdteMsg('QQ', '你好'))
print(forward.getQQRobotInfo())
