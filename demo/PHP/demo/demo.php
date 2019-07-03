<?php
//插件的提交返回地址中配为本文件的访问地址
namespace ksust\http_api\demo;
error_reporting(0);
header("Content-Type:text/html;Charset=utf8");//设置编码，必需
include '../HTTPSDK.php';
use ksust\http_api\HTTPSDK;

$sdk = HTTPSDK::httpGet();
$msg = $sdk->getMsg();//插件发送过来的消息
$sdk->sendPrivateMsg($msg['QQ'], '你发送了这样的消息：' . $msg['Msg']);//逻辑代码，向发送者回消息
echo $sdk->toJsonString();//命令返回

?>

