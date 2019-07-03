<?php

namespace ksust\http_api\demo;
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
include '../HTTPSDK.php';
use ksust\http_api\HTTPSDK;

//保证插件再2.2.2及以上并启动
$forward = HTTPSDK::msgForwardPush('QQ', 'code');//输入QQ和授权码（http://work.ksust.com）
var_dump($forward->getLoginQQ());//获取登录的QQ
var_dump($forward->sendPrivateMsg('QQ', 'hello~'));//向QQ发送消息
var_dump($forward->getFriendList());//获取好友列表
