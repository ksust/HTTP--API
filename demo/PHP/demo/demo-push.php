<?php
namespace ksust\http_api\demo;
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
include '../HTTPSDK.php';
use ksust\http_api\HTTPSDK;

//插件中开启推送，并设置端口为8080
$push = HTTPSDK::httpPush('http://127.0.0.1:8080');
var_dump($push->getLoginQQ());//获取登录的QQ
var_dump($push->sendPrivateMsg('QQ', 'hello~'));//向QQ发送消息
var_dump($push->getFriendList());//获取好友列表
