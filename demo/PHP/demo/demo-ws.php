<?php

namespace ksust\http_api\demo;
//websocket（结合使用workman）,插件的提交返回地址中配为相关地址（websocket://0.0.0.0:2346），命令行中启动本文件（php demo-ws.php restart）
//workman下载地址：https://www.workerman.net/download/workermanzip
header("Content-type:text/html;charset=utf-8");
include "../vendor/autoload.php";

use ksust\http_api\HTTPSDK;
use Workerman\Worker;

require_once '../HTTPSDK.php';
// Create a Websocket server
$ws_worker = new Worker("websocket://0.0.0.0:2346");
// 4 processes
$ws_worker->count = 4;
// Emitted when new connection come
$ws_worker->onConnect = function ($connection) {
    echo "New connection\n";
};
// Emitted when data received
$ws_worker->onMessage = function ($connection, $data) {
    $json = json_decode(urldecode(urldecode($data)), true);
    if (isset($json['type']) && $json['type'] == 'init') {
        $connection->send('{"type":"success"}');
    } else {
        //消息操作
        $sdk = HTTPSDK::webSocket($data);
        $msg = $sdk->getMsg();
        if ($msg['Msg'] == 'demo') {
            $sdk->sendPrivateMsg($msg['QQ'], '你发送了这样的消息：' . $msg['Msg']);//逻辑代码，向发送者回消息
        }
        //echo $sdk->returnJsonString();
        $connection->send($sdk->toJsonString());
    }

};
// Emitted when connection closed
$ws_worker->onClose = function ($connection) {
    echo "Connection closed\n";
};
// Run worker
Worker::runAll();