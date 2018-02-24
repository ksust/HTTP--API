include 'lib/HTTPSDK.php';
$sdk=new HTTPSDK();//获取操作对象并解析HTTP-API提交的消息
$msg=$sdk->getMsg();//获取传来的消息（array），详细请看数据结构
if($msg['Type']==HTTPSDK::$TYPE_FRIEND) {
    //私聊消息回复
    $sdk->sendPrivateMsg($msg['QQ'], '你发送了这样的消息:' . $msg['Msg']);//发送私聊消息
}
echo $sdk->returnJsonString();//最终返回，一定要输出