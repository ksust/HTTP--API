<?php

header("Content-Type:text/html;Charset=utf8");//设置编码，必需
$dataArr=json_decode(urldecode(urldecode((file_get_contents("php://input")))),true);//获得解码后的数组
//var_dump($dataArr);//输出获取消息
echo json_encode( ['data'=>[['Type' => 1, 'QQ' => 'QQ号', 'Msg' => 'Hello World!']]]);//向QQ发送私聊消息 Hello World！