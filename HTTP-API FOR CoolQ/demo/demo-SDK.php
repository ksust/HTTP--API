<?php
include 'lib/CQSDK.php';
$IR=new CQSDK("http://127.0.0.1:19688");//设置推送端口，本机测试
var_dump($IR->getGroupList());//获取并显示群列表
var_dump($IR->sendPrivateMsg('QQ号','Hello World!'));//向QQ发送私聊消息 Hello World！